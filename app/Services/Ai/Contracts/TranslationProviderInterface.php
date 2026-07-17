<?php

namespace App\Services\Ai\Contracts;

interface TranslationProviderInterface
{
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $context = []): string;
}
