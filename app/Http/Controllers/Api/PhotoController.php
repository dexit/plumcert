<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Job;
use App\Models\Task;
use App\Models\InspectionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index(Request $request)
    {
        $query = Photo::query();

        // Filter by photoable_type
        if ($request->has('photoable_type')) {
            $typeMap = [
                'job' => 'App\Models\Job',
                'task' => 'App\Models\Task',
                'inspection' => 'App\Models\InspectionItem',
            ];
            $photoableType = $typeMap[$request->photoable_type] ?? null;
            if ($photoableType) {
                $query->where('photoable_type', $photoableType);
            }
        }

        // Filter by photoable_id
        if ($request->has('photoable_id')) {
            $query->where('photoable_id', $request->photoable_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'photoable_type' => 'required|in:job,task,inspection',
            'photoable_id' => 'required|integer',
            'image' => 'required|image|max:10240',
            'caption' => 'nullable|string',
            'phase' => 'nullable|in:start,during,finish,defect',
        ]);

        // Map photoable_type string to full class name
        $typeMap = [
            'job' => 'App\Models\Job',
            'task' => 'App\Models\Task',
            'inspection' => 'App\Models\InspectionItem',
        ];
        $validated['photoable_type'] = $typeMap[$validated['photoable_type']];

        // Verify the photoable resource exists
        $modelClass = $validated['photoable_type'];
        if (!$modelClass::find($validated['photoable_id'])) {
            return response()->json(['error' => 'Photoable resource not found'], 422);
        }

        // Store the uploaded file
        $path = $request->file('image')->store('photos', 'public');

        // Create photo record
        $photo = Photo::create([
            'photoable_type' => $validated['photoable_type'],
            'photoable_id' => $validated['photoable_id'],
            'uploaded_by_user_id' => $request->user()->id,
            'path' => $path,
            'caption' => $validated['caption'] ?? null,
            'phase' => $validated['phase'] ?? 'during',
        ]);

        return response()->json($photo, 201);
    }

    public function destroy(Photo $photo)
    {
        // Delete file from storage if it exists
        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();
        return response()->json(null, 204);
    }
}
