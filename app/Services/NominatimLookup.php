<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Thin wrapper around the OpenStreetMap Nominatim geocoder used to power
 * address autocomplete in the admin forms. Results are encoded into the
 * Select option key so the chosen address can be split into fields without
 * a second network round-trip.
 */
class NominatimLookup
{
    private const ENDPOINT = 'https://nominatim.openstreetmap.org/search';

    /**
     * Return Filament Select options: [encodedComponents => displayLabel].
     *
     * @return array<string, string>
     */
    public static function options(string $search): array
    {
        $search = trim($search);
        if (strlen($search) < 3) {
            return [];
        }

        try {
            $response = Http::withUserAgent('plumcert/1.0')
                ->timeout(8)
                ->get(self::ENDPOINT, [
                    'q'              => $search,
                    'countrycodes'   => 'gb',
                    'format'         => 'json',
                    'addressdetails' => 1,
                    'limit'          => 8,
                ]);

            if (! $response->successful()) {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }

        $options = [];

        foreach ($response->json() ?? [] as $item) {
            $components = self::parse($item);
            $label = $item['display_name'] ?? $components['line1'];
            $options[self::encode($components)] = $label;
        }

        return $options;
    }

    /**
     * Decode a selected option key back into address components.
     *
     * @return array{line1:string, town:string, county:string, postcode:string}|null
     */
    public static function decode(?string $key): ?array
    {
        if (! $key) {
            return null;
        }

        $json = base64_decode($key, true);
        if ($json === false) {
            return null;
        }

        $data = json_decode($json, true);

        return is_array($data) ? $data : null;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{line1:string, town:string, county:string, postcode:string}
     */
    private static function parse(array $item): array
    {
        $a = $item['address'] ?? [];

        $line1 = trim(implode(' ', array_filter([
            $a['house_number'] ?? null,
            $a['road'] ?? $a['pedestrian'] ?? $a['neighbourhood'] ?? null,
        ])));

        if ($line1 === '') {
            $line1 = $a['suburb'] ?? $a['village'] ?? $item['display_name'] ?? '';
        }

        return [
            'line1'    => $line1,
            'town'     => $a['city'] ?? $a['town'] ?? $a['village'] ?? $a['suburb'] ?? '',
            'county'   => $a['county'] ?? $a['state_district'] ?? $a['state'] ?? '',
            'postcode' => $a['postcode'] ?? '',
        ];
    }

    /**
     * @param  array{line1:string, town:string, county:string, postcode:string}  $components
     */
    private static function encode(array $components): string
    {
        return base64_encode(json_encode($components, JSON_THROW_ON_ERROR));
    }
}
