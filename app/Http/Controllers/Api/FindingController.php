<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Finding;
use Illuminate\Http\Request;

class FindingController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Finding::paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'description' => 'required|string',
            'severity' => 'nullable|in:low,medium,high',
            'before_image' => 'nullable|image',
            'after_image' => 'nullable|image',
        ]);

        $finding = Finding::create($validated);

        if ($request->hasFile('before_image')) {
            $path = $request->file('before_image')->store('findings/before', 'public');
            $finding->update(['before_image_path' => $path]);
        }

        if ($request->hasFile('after_image')) {
            $path = $request->file('after_image')->store('findings/after', 'public');
            $finding->update(['after_image_path' => $path]);
        }

        return response()->json($finding, 201);
    }

    public function show(Finding $finding)
    {
        return response()->json($finding);
    }

    public function update(Request $request, Finding $finding)
    {
        $validated = $request->validate([
            'description' => 'sometimes|string',
            'severity' => 'sometimes|in:low,medium,high',
        ]);

        $finding->update($validated);
        return response()->json($finding);
    }

    public function destroy(Finding $finding)
    {
        $finding->delete();
        return response()->json(null, 204);
    }
}
