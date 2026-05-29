<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Job;
use App\Models\Certificate;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function pull(Request $request)
    {
        $since = $request->input('since') ? now()->parse($request->input('since')) : now()->subMonths(1);

        return response()->json([
            'customers' => Customer::where('updated_at', '>=', $since)->get(),
            'properties' => Property::where('updated_at', '>=', $since)->get(),
            'jobs' => Job::where('updated_at', '>=', $since)->get(),
            'certificates' => Certificate::where('updated_at', '>=', $since)->get(),
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function push(Request $request)
    {
        $validated = $request->validate([
            'customers' => 'sometimes|array',
            'properties' => 'sometimes|array',
            'jobs' => 'sometimes|array',
            'certificates' => 'sometimes|array',
        ]);

        $results = ['upserted' => 0, 'errors' => []];

        // Simple upsert with last-write-wins
        if (isset($validated['customers'])) {
            foreach ($validated['customers'] as $data) {
                try {
                    Customer::updateOrCreate(
                        ['id' => $data['id'] ?? null],
                        $data
                    );
                    $results['upserted']++;
                } catch (\Exception $e) {
                    $results['errors'][] = $e->getMessage();
                }
            }
        }

        return response()->json([
            'results' => $results,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
