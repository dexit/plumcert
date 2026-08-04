<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistant
{
    private string $apiKey;
    private string $model = 'claude-haiku-4-5-20251001';
    private string $baseUrl = 'https://api.anthropic.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key', '');
    }

    /**
     * Generate quote line items from a job description.
     * Returns array of ['description'=>string, 'qty'=>int, 'unit_price'=>float]
     */
    public function generateQuoteItems(string $jobDescription, string $jobType = 'general'): array
    {
        $prompt = <<<PROMPT
You are an expert gas engineer in the UK. Based on this job description, generate realistic quote line items.

Job type: {$jobType}
Job description: {$jobDescription}

Respond with ONLY a JSON array of line items, no other text. Each item must have:
- "description": string (clear description of work or part)
- "qty": number (quantity, usually 1 for labour, could be more for parts)
- "unit_price": number (realistic UK price in GBP, no £ symbol)

Example: [{"description":"Annual boiler service","qty":1,"unit_price":85.00},{"description":"New thermostat","qty":1,"unit_price":45.00},{"description":"Labour - 1.5 hours","qty":1.5,"unit_price":65.00}]

Be realistic with UK 2024 prices. Include labour separately. Include call-out charge if emergency.
PROMPT;

        return $this->ask($prompt, []);
    }

    /**
     * Suggest fault diagnosis from reported symptoms.
     * Returns ['diagnosis'=>string, 'likely_cause'=>string, 'recommended_action'=>string, 'severity'=>string]
     */
    public function diagnoseFault(string $faultReported, string $applianceName = ''): array
    {
        $prompt = <<<PROMPT
You are an expert Gas Safe registered engineer in the UK. Based on these reported symptoms, provide a diagnostic assessment.

Appliance: {$applianceName}
Fault reported: {$faultReported}

Respond with ONLY a JSON object, no other text:
{
  "likely_cause": "one sentence describing most probable cause",
  "diagnosis": "brief technical diagnosis (2-3 sentences)",
  "recommended_action": "what the engineer should check/do first",
  "severity": "low|medium|high|immediately_dangerous",
  "safety_note": "any immediate safety warning or null"
}
PROMPT;

        $default = [
            'likely_cause' => '',
            'diagnosis' => '',
            'recommended_action' => '',
            'severity' => 'medium',
            'safety_note' => null,
        ];

        return $this->ask($prompt, $default);
    }

    private function ask(string $prompt, array $default): array
    {
        if (empty($this->apiKey)) {
            Log::channel('plumcert')->info('AiAssistant: no API key configured, returning empty result');
            return $default;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/messages", [
                'model'      => $this->model,
                'max_tokens' => 1024,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->failed()) {
                Log::channel('plumcert')->error('AiAssistant API error', ['status' => $response->status(), 'body' => $response->body()]);
                return $default;
            }

            $content = $response->json('content.0.text', '');
            // Strip markdown code fences if present
            $content = preg_replace('/^```(?:json)?\n?|\n?```$/s', '', trim($content));
            return json_decode($content, true) ?? $default;

        } catch (\Throwable $e) {
            Log::channel('plumcert')->error('AiAssistant exception', ['error' => $e->getMessage()]);
            return $default;
        }
    }
}
