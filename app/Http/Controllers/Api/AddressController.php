<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');

        if (!$q || strlen($q) < 2) {
            return response()->json([]);
        }

        try {
            $response = Http::withUserAgent('plumcert/1.0')->get(
                'https://nominatim.openstreetmap.org/search',
                [
                    'q' => $q,
                    'countrycodes' => 'gb',
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 8,
                ]
            );

            $results = collect($response->json())->map(fn ($item) => [
                'address' => $item['display_name'] ?? '',
                'lat' => $item['lat'] ?? null,
                'lon' => $item['lon'] ?? null,
                'postcode' => $item['address']['postcode'] ?? null,
            ])->values();

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}
