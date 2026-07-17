<?php

namespace App\Services\Ai;

use App\Services\Ai\Contracts\AiProviderInterface;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\GroqProvider;
use App\Services\Ai\Providers\OpenAiProvider;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AiManager
{
    public function provider(?string $name = null): AiProviderInterface
    {
        $providerName = strtolower(
            trim(
                $name ?? config('ai.provider', 'gemini')
            )
        );

        return match ($providerName) {
            'gemini' => app(GeminiProvider::class),
            'groq' => app(GroqProvider::class),
            'openai' => app(OpenAiProvider::class),

            default => throw new InvalidArgumentException(
                "Fournisseur IA inconnu : {$providerName}"
            ),
        };
    }

    public function generateJson(
        string $system,
        string $user,
        array $schema,
        ?string $provider = null
    ): array {
        return $this->executeWithFallback(
            function (AiProviderInterface $aiProvider) use (
                $system,
                $user,
                $schema
            ): array {
                return $aiProvider->generateJson(
                    $system,
                    $user,
                    $schema
                );
            },
            $provider
        );
    }

    public function generateText(
        string $system,
        string $user,
        ?string $provider = null
    ): string {
        return $this->executeWithFallback(
            function (AiProviderInterface $aiProvider) use (
                $system,
                $user
            ): string {
                return $aiProvider->generateText(
                    $system,
                    $user
                );
            },
            $provider
        );
    }

    private function executeWithFallback(
        callable $callback,
        ?string $provider = null
    ): mixed {
        $primaryProvider = $provider
            ?? config('ai.provider', 'gemini');

        try {
            return $callback(
                $this->provider($primaryProvider)
            );
        } catch (Throwable $primaryException) {
            if ($provider !== null) {
                throw $primaryException;
            }

            $fallbackProvider = config(
                'ai.fallback_provider'
            );

            if (
                !is_string($fallbackProvider) ||
                trim($fallbackProvider) === '' ||
                $fallbackProvider === $primaryProvider
            ) {
                throw $primaryException;
            }

            report($primaryException);

            try {
                return $callback(
                    $this->provider($fallbackProvider)
                );
            } catch (Throwable $fallbackException) {
                report($fallbackException);

                throw new RuntimeException(
                    sprintf(
                        'Erreur %s : %s | Erreur %s : %s',
                        $primaryProvider,
                        $primaryException->getMessage(),
                        $fallbackProvider,
                        $fallbackException->getMessage()
                    ),
                    previous: $fallbackException
                );
            }
        }
    }
}
