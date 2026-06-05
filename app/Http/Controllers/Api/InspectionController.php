<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionItem;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $query = InspectionItem::query();

        // Filter by job_id
        if ($request->has('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Eager load photos
        $query->with('photos');

        return response()->json($query->get());
    }

    public function show(InspectionItem $inspection)
    {
        return response()->json($inspection->load('photos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:service_jobs,id',
            'certificate_id' => 'nullable|exists:certificates,id',
            'property_id' => 'nullable|exists:properties,id',
            'category' => 'required|in:gas_appliance,gas_boiler,heater,plumbing,radiators,co_alarm,smoke_alarm',
            'location' => 'nullable|string',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'serial' => 'nullable|string',
            'gc_number' => 'nullable|string',
            'result' => 'nullable|in:pass,fail,at_risk,id,na',
            'data' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $inspection = InspectionItem::create($validated);
        return response()->json($inspection->load('photos'), 201);
    }

    public function update(Request $request, InspectionItem $inspection)
    {
        $validated = $request->validate([
            'category' => 'sometimes|in:gas_appliance,gas_boiler,heater,plumbing,radiators,co_alarm,smoke_alarm',
            'location' => 'sometimes|nullable|string',
            'make' => 'sometimes|nullable|string',
            'model' => 'sometimes|nullable|string',
            'serial' => 'sometimes|nullable|string',
            'gc_number' => 'sometimes|nullable|string',
            'result' => 'sometimes|nullable|in:pass,fail,at_risk,id,na',
            'data' => 'sometimes|nullable|array',
            'notes' => 'sometimes|nullable|string',
        ]);

        $inspection->update($validated);
        return response()->json($inspection->load('photos'));
    }

    public function destroy(InspectionItem $inspection)
    {
        $inspection->delete();
        return response()->json(null, 204);
    }

    public function categories()
    {
        return response()->json(InspectionItem::CATEGORIES);
    }
}
