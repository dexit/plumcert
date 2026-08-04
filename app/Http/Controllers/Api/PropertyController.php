<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Customer $customer)
    {
        return response()->json($customer->properties);
    }

    public function store(Customer $customer, Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string',
            'postcode' => 'required|string',
            'type' => 'nullable|string',
        ]);

        $property = $customer->properties()->create($validated);
        return response()->json($property, 201);
    }

    public function show(Property $property)
    {
        return response()->json($property);
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'address' => 'sometimes|string',
            'postcode' => 'sometimes|string',
            'type' => 'sometimes|string',
        ]);

        $property->update($validated);
        return response()->json($property);
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return response()->json(null, 204);
    }

    public function searchByPostcode(Request $request)
    {
        $postcode = $request->input('postcode');
        
        if (!$postcode) {
            return response()->json([]);
        }

        return response()->json(
            Property::where('postcode', 'like', "%$postcode%")->get()
        );
    }
}
