<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Boiler;
use App\Models\Property;
use Illuminate\Http\Request;

class BoilerController extends Controller
{
    public function index(Property $property)
    {
        return response()->json($property->boilers);
    }

    public function store(Property $property, Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'type' => 'required|string',
            'serial' => 'nullable|string',
            'commissioned_at' => 'nullable|date',
        ]);

        $boiler = $property->boilers()->create($validated);
        return response()->json($boiler, 201);
    }

    public function show(Boiler $boiler)
    {
        return response()->json($boiler);
    }

    public function update(Request $request, Boiler $boiler)
    {
        $validated = $request->validate([
            'make' => 'sometimes|string',
            'model' => 'sometimes|string',
            'type' => 'sometimes|string',
            'serial' => 'sometimes|string',
            'commissioned_at' => 'sometimes|date',
        ]);

        $boiler->update($validated);
        return response()->json($boiler);
    }

    public function destroy(Boiler $boiler)
    {
        $boiler->delete();
        return response()->json(null, 204);
    }
}
