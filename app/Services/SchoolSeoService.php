<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SchoolSeoService
{
    public function generateSeoData(string $pageName, string $dynamicContext = ''): array
    {
        $apiKey = config('services.openai.key');

        // 1. Check if key even exists
        if (empty($apiKey)) {
            throw new \Exception("OpenAI API Key is missing. Please add OPENAI_API_KEY to your .env file.");
        }

        $formattedPageName = ucwords(str_replace('_', ' ', $pageName));

        $facts = trim(implode("\n", array_filter([
            "School Name: Barchhain Secondary School",
            "Location: Badikedar-2, Barchhain, Doti, Sudurpashchim Province, Nepal",
            "Page Name: {$formattedPageName}",
            "Page Specific Data: {$dynamicContext}",
        ])));

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an elite SEO expert for educational institutions in Nepal. Return ONLY valid JSON."
            ],
            [
                'role' => 'user',
                'content' => 
                    "Generate a highly optimized SEO pack for a specific web page of Barchhain Secondary School.\n\n" .
                    "CRITICAL RULES:\n" .
                    "1. meta_title: Exactly 50-60 characters. Must be unique to the Page Name and include 'Barchhain' or 'Doti'.\n" .
                    "2. meta_description: Exactly 150-160 characters. Write a compelling, natural sentence specific to the Page Specific Data. Include a Call to Action.\n" .
                    "3. meta_keywords: Provide 15 to 20 comma-separated keywords. Mix high-volume terms with long-tail niche terms specific to the page.\n" .
                    "4. Return ONLY a JSON object with keys: meta_title, meta_description, meta_keywords.\n\n" .
                    "FACTS TO USE:\n{$facts}"
            ],
        ];

        // 2. Make the HTTP request via OpenRouter (OpenAI-compatible endpoint)
        $response = Http::withToken($apiKey)
            ->withHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-Title'      => config('app.name'),
            ])
            ->timeout(45)
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'           => 'openai/gpt-4o-mini',
                'response_format' => ['type' => 'json_object'],
                'messages'        => $messages,
                'temperature'     => 0.4,
            ]);

        // 3. Catch API errors
        if ($response->failed()) {
            $errorDetails = $response->json('error.message') ?? $response->body();
            throw new \Exception("OpenRouter API Error: " . $errorDetails);
        }

        $text = $response->json('choices.0.message.content');
        $data = json_decode($text, true);

        // 4. Ensure the response is in the right format
        if (!is_array($data) || empty($data['meta_title'])) {
            throw new \Exception("API returned invalid data format. Please try again.");
        }

        return [
            'meta_title'       => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'meta_keywords'    => $data['meta_keywords'],
        ];
    }
}
