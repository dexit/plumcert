<?php
namespace Tests\Unit;

use App\Services\AiAssistant;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    public function test_returns_empty_array_when_no_api_key(): void
    {
        config(['services.anthropic.api_key' => '']);
        $ai = new AiAssistant();
        $result = $ai->generateQuoteItems('Annual boiler service');
        $this->assertSame([], $result);
    }

    public function test_parses_valid_api_response(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);

        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'content' => [['type' => 'text', 'text' => '[{"description":"Boiler service","qty":1,"unit_price":85.00}]']],
            ], 200),
        ]);

        $ai = new AiAssistant();
        $result = $ai->generateQuoteItems('Annual boiler service');

        $this->assertCount(1, $result);
        $this->assertSame('Boiler service', $result[0]['description']);
        $this->assertSame(85.00, $result[0]['unit_price']);
    }

    public function test_returns_default_on_api_error(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);

        Http::fake(['api.anthropic.com/*' => Http::response([], 500)]);

        $ai = new AiAssistant();
        $result = $ai->generateQuoteItems('test');
        $this->assertSame([], $result);
    }
}
