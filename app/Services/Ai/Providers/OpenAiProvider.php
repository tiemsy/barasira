<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiProvider implements AiProviderInterface
{
    public function name(): string { return 'openai'; }

    public function generateJson(string $systemPrompt, string $userPrompt, array $schema): array
    {
        $payload = $this->basePayload($systemPrompt, $userPrompt);
        $payload['response_format'] = [
            'type' => 'json_schema',
            'json_schema' => [
                'name' => 'barasira_payload',
                'strict' => true,
                'schema' => $schema,
            ],
        ];
        return $this->decodeJson($this->request($payload));
    }

    public function generateText(string $systemPrompt, string $userPrompt): string
    {
        return trim($this->request($this->basePayload($systemPrompt, $userPrompt)));
    }

    private function basePayload(string $systemPrompt, string $userPrompt): array
    {
        return [
            'model' => config('ai.providers.openai.model'),
            'temperature' => 0.2,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ];
    }

    private function request(array $payload): string
    {
        $config = config('ai.providers.openai');
        if (blank($config['key'] ?? null)) {
            throw new RuntimeException('OPENAI_API_KEY n’est pas configurée.');
        }
        $response = Http::withToken($config['key'])->acceptJson()->asJson()
            ->timeout(config('ai.timeout'))->retry(2, 500, throw: false)
            ->post(rtrim($config['base_url'], '/') . '/chat/completions', $payload);
        if ($response->failed()) {
            throw new RuntimeException('Erreur OpenAI (' . $response->status() . ') : ' . $response->body());
        }
        $text = $response->json('choices.0.message.content');
        if (!is_string($text) || trim($text) === '') {
            throw new RuntimeException('OpenAI n’a retourné aucun contenu.');
        }
        return $text;
    }

    private function decodeJson(string $text): array
    {
        $data = json_decode($text, true);
        if (!is_array($data)) throw new RuntimeException('Le JSON OpenAI est invalide.');
        return $data;
    }
}
