<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Models\TaskTemplate;
use Illuminate\Database\Eloquent\Model;

/**
 * Generates default task/todo lists for jobs, quotes and certificates.
 * Uses TaskTemplate rows when present, otherwise falls back to sensible defaults.
 */
class TaskGenerator
{
    /**
     * Default task sets keyed by trigger. offset_days relative to the anchor date.
     *
     * @var array<string, array<int, array{title:string, offset:int, photo:bool}>>
     */
    private const DEFAULTS = [
        'job' => [
            ['title' => 'Confirm appointment with customer', 'offset' => -1, 'photo' => false],
            ['title' => 'Arrive & photograph site (before)', 'offset' => 0, 'photo' => true],
            ['title' => 'Carry out inspection / works', 'offset' => 0, 'photo' => false],
            ['title' => 'Photograph completed work (after)', 'offset' => 0, 'photo' => true],
            ['title' => 'Issue certificate / paperwork', 'offset' => 0, 'photo' => false],
            ['title' => 'Take payment / send invoice', 'offset' => 0, 'photo' => false],
        ],
        'quote' => [
            ['title' => 'Survey property & gather requirements', 'offset' => 0, 'photo' => true],
            ['title' => 'Prepare & send written quote', 'offset' => 1, 'photo' => false],
            ['title' => 'Follow up with customer', 'offset' => 3, 'photo' => false],
        ],
        'certificate' => [
            ['title' => 'Complete all appliance readings', 'offset' => 0, 'photo' => false],
            ['title' => 'Capture defect photos (if any)', 'offset' => 0, 'photo' => true],
            ['title' => 'Obtain customer signature', 'offset' => 0, 'photo' => false],
            ['title' => 'Email certificate to customer', 'offset' => 0, 'photo' => false],
        ],
    ];

    public function generate(Model $model, string $trigger, ?string $appliesTo = null, ?\DateTimeInterface $anchor = null): int
    {
        // skip if tasks already exist for this record
        if (method_exists($model, 'tasks') && $model->tasks()->exists()) {
            return 0;
        }

        $anchor ??= now();
        $templates = TaskTemplate::query()->for($trigger, $appliesTo)->get();

        if ($templates->isNotEmpty()) {
            $sort = 0;
            foreach ($templates as $tpl) {
                $model->tasks()->create([
                    'title'          => $tpl->title,
                    'description'    => $tpl->description,
                    'status'         => 'pending',
                    'due_at'         => (clone $anchor)->modify("{$tpl->offset_days} days"),
                    'sort_order'     => $tpl->sort_order ?: $sort++,
                    'is_auto'        => true,
                    'photo_required' => $tpl->photo_required,
                ]);
            }

            return $templates->count();
        }

        // fallback to hard-coded defaults
        $defaults = self::DEFAULTS[$trigger] ?? [];
        foreach ($defaults as $i => $d) {
            $model->tasks()->create([
                'title'          => $d['title'],
                'status'         => 'pending',
                'due_at'         => (clone $anchor)->modify("{$d['offset']} days"),
                'sort_order'     => $i,
                'is_auto'        => true,
                'photo_required' => $d['photo'],
            ]);
        }

        return count($defaults);
    }
}
