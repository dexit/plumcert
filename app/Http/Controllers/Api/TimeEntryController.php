<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\TimeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function index(Job $job): JsonResponse
    {
        return response()->json($job->timeEntries()->with('user:id,name')->get());
    }

    public function clockIn(Request $request, Job $job): JsonResponse
    {
        $open = TimeEntry::where('job_id', $job->id)
            ->where('user_id', $request->user()->id)
            ->whereNull('clocked_out_at')
            ->first();

        if ($open) {
            return response()->json(['message' => 'Already clocked in', 'entry' => $open], 409);
        }

        $entry = TimeEntry::create([
            'job_id'        => $job->id,
            'user_id'       => $request->user()->id,
            'clocked_in_at' => now(),
        ]);

        return response()->json(['message' => 'Clocked in', 'entry' => $entry], 201);
    }

    public function clockOut(Request $request, Job $job): JsonResponse
    {
        $entry = TimeEntry::where('job_id', $job->id)
            ->where('user_id', $request->user()->id)
            ->whereNull('clocked_out_at')
            ->first();

        if (! $entry) {
            return response()->json(['message' => 'Not clocked in'], 404);
        }

        $entry->update([
            'clocked_out_at' => now(),
            'minutes'        => $entry->clocked_in_at->diffInMinutes(now()),
            'notes'          => $request->input('notes'),
        ]);

        return response()->json(['message' => 'Clocked out', 'entry' => $entry, 'duration' => $entry->durationFormatted()]);
    }
}
