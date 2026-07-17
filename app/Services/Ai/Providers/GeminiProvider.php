<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\Contracts\AiProviderInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JsonException;
use RuntimeException;
use Throwable;

class GeminiProvider implements AiProviderInterface
{
    public function name(): string
    {
        return 'gemini';
    }

    /**
     * Génère une réponse structurée au format JSON.
     *
     * @param array<string, mixed> $schema
     *
     * @return array<string, mixed>
     */
    public function generateJson(
        string $systemPrompt,
        string $userPrompt,
        array $schema
    ): array {
        $response = $this->request([
            'system_instruction' => [
                'parts' => [
                    [
                        'text' => $systemPrompt,
                    ],
                ],
            ],

            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => $userPrompt,
                        ],
                    ],
                ],
            ],

            'generationConfig' => [
                'temperature' => 0.2,
                'responseMimeType' => 'application/json',
                'responseJsonSchema' => $schema,
            ],
        ]);

        return $this->decodeJson(
            $this->extractText($response)
        );
    }

    /**
     * Génère une réponse textuelle simple.
     */
    public function generateText(
        string $systemPrompt,
        string $userPrompt
    ): string {
        $response = $this->request([
            'system_instruction' => [
                'parts' => [
                    [
                        'text' => $systemPrompt,
                    ],
                ],
            ],

            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => $userPrompt,
                        ],
                    ],
                ],
            ],

            'generationConfig' => [
                'temperature' => 0.2,
            ],
        ]);

        return $this->extractText($response);
    }

    /**
     * Envoie une requête à l’API Gemini.
     *
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function request(array $payload): array
    {
        $config = config('ai.providers.gemini', []);

        $apiKey = trim((string) ($config['key'] ?? ''));
        $model = trim((string) ($config['model'] ?? ''));
        $baseUrl = rtrim(
            trim((string) ($config['base_url'] ?? '')),
            '/'
        );

        if ($apiKey === '') {
            throw new RuntimeException(
                'GEMINI_API_KEY n’est pas configurée.'
            );
        }

        if ($model === '') {
            throw new RuntimeException(
                'GEMINI_MODEL n’est pas configuré.'
            );
        }

        if ($baseUrl === '') {
            throw new RuntimeException(
                'GEMINI_BASE_URL n’est pas configurée.'
            );
        }

        $url = sprintf(
            '%s/models/%s:generateContent',
            $baseUrl,
            rawurlencode($model)
        );

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                ])
                ->connectTimeout(
                    (int) config('ai.connect_timeout', 10)
                )
                ->timeout(
                    (int) config('ai.timeout', 45)
                )
                ->retry(
                    times: 3,
                    sleepMilliseconds: function (
                        int $attempt,
                        Throwable $exception
                    ): int {
                        return $attempt * 500;
                    },
                    when: static function (
                        Throwable $exception,
                        PendingRequest $request
                    ): bool {
                        if ($exception instanceof ConnectionException) {
                            return true;
                        }

                        if (! $exception instanceof RequestException) {
                            return false;
                        }

                        $response = $exception->response;

                        return $response->status() === 429
                            || $response->serverError();
                    },
                    throw: false
                )
                ->post($url, $payload);
        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'Impossible de contacter l’API Gemini : '
                    . $exception->getMessage(),
                previous: $exception
            );
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'Une erreur est survenue pendant la requête Gemini : '
                    . $exception->getMessage(),
                previous: $exception
            );
        }

        if ($response->failed()) {
            throw new RuntimeException(
                $this->buildErrorMessage($response)
            );
        }

        $json = $response->json();

        if (! is_array($json)) {
            throw new RuntimeException(
                'Gemini a retourné une réponse JSON invalide.'
            );
        }

        return $json;
    }

    /**
     * Extrait le texte retourné par Gemini.
     *
     * @param array<string, mixed> $response
     */
    private function extractText(array $response): string
    {
        $text = data_get(
            $response,
            'candidates.0.content.parts.0.text'
        );

        if (is_string($text) && trim($text) !== '') {
            return trim($text);
        }

        $finishReason = data_get(
            $response,
            'candidates.0.finishReason'
        );

        $blockReason = data_get(
            $response,
            'promptFeedback.blockReason'
        );

        $blockMessage = data_get(
            $response,
            'promptFeedback.blockReasonMessage'
        );

        if (is_string($blockReason) && $blockReason !== '') {
            $message = sprintf(
                'La requête Gemini a été bloquée : %s.',
                $blockReason
            );

            if (
                is_string($blockMessage)
                && trim($blockMessage) !== ''
            ) {
                $message .= ' ' . trim($blockMessage);
            }

            throw new RuntimeException($message);
        }

        if (is_string($finishReason) && $finishReason !== '') {
            throw new RuntimeException(
                sprintf(
                    'Gemini n’a retourné aucun texte. Motif de fin : %s.',
                    $finishReason
                )
            );
        }

        throw new RuntimeException(
            'Gemini n’a retourné aucun texte exploitable.'
        );
    }

    /**
     * Décode le texte JSON retourné par Gemini.
     *
     * @return array<string, mixed>
     */
    private function decodeJson(string $text): array
    {
        $text = $this->removeMarkdownCodeBlock($text);

        try {
            $data = json_decode(
                $text,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'Le JSON retourné par Gemini est invalide : '
                    . $exception->getMessage(),
                previous: $exception
            );
        }

        if (! is_array($data)) {
            throw new RuntimeException(
                'Gemini n’a pas retourné un objet JSON valide.'
            );
        }

        return $data;
    }

    /**
     * Supprime un éventuel bloc Markdown ```json.
     */
    private function removeMarkdownCodeBlock(string $text): string
    {
        $text = trim($text);

        if (! str_starts_with($text, '```')) {
            return $text;
        }

        $text = preg_replace(
            '/^```(?:json)?\s*/i',
            '',
            $text
        );

        $text = preg_replace(
            '/\s*```$/',
            '',
            (string) $text
        );

        return trim((string) $text);
    }

    /**
     * Construit un message d’erreur lisible à partir de la réponse Gemini.
     */
    private function buildErrorMessage(Response $response): string
    {
        $json = $response->json();

        $message = is_array($json)
            ? data_get($json, 'error.message')
            : null;

        $status = is_array($json)
            ? data_get($json, 'error.status')
            : null;

        if (! is_string($message) || trim($message) === '') {
            $message = trim($response->body());
        }

        if ($message === '') {
            $message = 'Erreur inconnue retournée par Gemini.';
        }

        if (is_string($status) && trim($status) !== '') {
            return sprintf(
                'Erreur Gemini (%d - %s) : %s',
                $response->status(),
                trim($status),
                trim($message)
            );
        }

        return sprintf(
            'Erreur Gemini (%d) : %s',
            $response->status(),
            trim($message)
        );
    }
}
