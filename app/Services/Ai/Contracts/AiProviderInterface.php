<?php

namespace App\Services\Ai\Contracts;

interface AiProviderInterface
{
    public function generateJson(string $systemPrompt, string $userPrompt, array $schema): array;
    public function generateText(string $systemPrompt, string $userPrompt): string;
    public function name(): string;
}
