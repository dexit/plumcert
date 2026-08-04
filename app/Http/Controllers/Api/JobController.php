<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Job::paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'property_id' => 'nullable|exists:properties,id',
            'type' => 'required|string',
            'status' => 'sometimes|in:open,scheduled,in_progress,completed,cancelled',
            'scheduled_at' => 'nullable|date',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $job = Job::create($validated);
        return response()->json($job, 201);
    }

    public function show(Job $job)
    {
        return response()->json($job);
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'type' => 'sometimes|string',
            'status' => 'sometimes|in:open,scheduled,in_progress,completed,cancelled',
            'scheduled_at' => 'sometimes|date',
            'assigned_to_user_id' => 'sometimes|exists:users,id',
            'notes' => 'sometimes|string',
        ]);

        $job->update($validated);
        return response()->json($job);
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return response()->json(null, 204);
    }

    public function today(Request $request)
    {
        $user = $request->user();
        return response()->json(
            Job::whereDate('scheduled_at', today())
                ->where('assigned_to_user_id', $user->id)
                ->get()
        );
    }

    public function thisWeek(Request $request)
    {
        $user = $request->user();
        return response()->json(
            Job::whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->where('assigned_to_user_id', $user->id)
                ->get()
        );
    }

    public function complete(Request $request, Job $job)
    {
        $job->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json($job);
    }
}
