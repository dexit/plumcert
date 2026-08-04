<?php

namespace Tests\Unit;

use App\Services\NominatimLookup;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NominatimLookupTest extends TestCase
{
    public function test_decode_roundtrips_encoded_components(): void
    {
        $encoded = base64_encode(json_encode([
            'line1'    => '10 High Street',
            'town'     => 'Leeds',
            'county'   => 'West Yorkshire',
            'postcode' => 'LS1 1AA',
        ]));

        $decoded = NominatimLookup::decode($encoded);

        $this->assertSame('10 High Street', $decoded['line1']);
        $this->assertSame('Leeds', $decoded['town']);
        $this->assertSame('LS1 1AA', $decoded['postcode']);
    }

    public function test_decode_returns_null_for_garbage(): void
    {
        $this->assertNull(NominatimLookup::decode(null));
        $this->assertNull(NominatimLookup::decode('!!!not-base64-json!!!'));
    }

    public function test_short_query_skips_network_call(): void
    {
        Http::fake();

        $this->assertSame([], NominatimLookup::options('a'));

        Http::assertNothingSent();
    }

    public function test_options_parse_nominatim_results(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'display_name' => '10, High Street, Leeds, West Yorkshire, LS1 1AA',
                    'lat' => '53.8', 'lon' => '-1.5',
                    'address' => [
                        'house_number' => '10',
                        'road' => 'High Street',
                        'city' => 'Leeds',
                        'county' => 'West Yorkshire',
                        'postcode' => 'LS1 1AA',
                    ],
                ],
            ]),
        ]);

        $options = NominatimLookup::options('10 High Street Leeds');

        $this->assertCount(1, $options);
        $key = array_key_first($options);
        $decoded = NominatimLookup::decode($key);
        $this->assertSame('10 High Street', $decoded['line1']);
        $this->assertSame('LS1 1AA', $decoded['postcode']);
    }
}
