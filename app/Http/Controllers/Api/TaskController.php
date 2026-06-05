<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Job;
use App\Models\Quote;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned to current user
        if ($request->boolean('assigned_to_me')) {
            $query->where('assigned_to_user_id', $request->user()->id);
        }

        // Filter by job_id
        if ($request->has('job_id')) {
            $query->where('taskable_type', 'App\Models\Job')
                  ->where('taskable_id', $request->job_id);
        }

        // Eager load photos
        $query->with('photos');

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'taskable_type' => 'required|in:job,quote',
            'taskable_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
            'photo_required' => 'nullable|boolean',
        ]);

        // Map taskable_type string to full class name
        $typeMap = [
            'job' => 'App\Models\Job',
            'quote' => 'App\Models\Quote',
        ];
        $validated['taskable_type'] = $typeMap[$validated['taskable_type']];

        // Verify the taskable resource exists
        $modelClass = $validated['taskable_type'];
        if (!$modelClass::find($validated['taskable_id'])) {
            return response()->json(['error' => 'Taskable resource not found'], 422);
        }

        $task = Task::create($validated);
        return response()->json($task->load('photos'), 201);
    }

    public function show(Task $task)
    {
        return response()->json($task->load('photos'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|string',
            'due_at' => 'sometimes|nullable|date',
        ]);

        $task->update($validated);
        return response()->json($task->load('photos'));
    }

    public function start(Task $task)
    {
        $task->update(['status' => 'in_progress']);
        return response()->json($task->load('photos'));
    }

    public function complete(Task $task)
    {
        $task->markDone();
        return response()->json($task->load('photos'));
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }
}
