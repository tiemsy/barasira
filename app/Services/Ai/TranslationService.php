<?php

namespace App\Services\Ai;

use App\Models\AiTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    public function __construct(private readonly AiManager $ai) {}

    public function translate(string $text, string $source, string $target, array $context = []): string
    {
        if ($source === $target || trim($text) === '') return $text;

        $cacheKey = 'ai_translation:' . hash('sha256', json_encode([$text, $source, $target, $context]));

        return Cache::remember($cacheKey, config('ai.cache_ttl'), function () use ($text, $source, $target, $context) {
            $system = 'Tu es le traducteur de BaraSira. Traduis fidèlement, naturellement et sans commentaire. '
                . 'Conserve les noms propres, montants, dates, balises et sauts de ligne. '
                . 'N’ajoute aucune information.';
            $user = "Langue source: {$source}\nLangue cible: {$target}\nContexte: "
                . json_encode($context, JSON_UNESCAPED_UNICODE)
                . "\nTexte:\n{$text}";

            return $this->ai->generateText($system, $user, config('ai.translation_provider'));
        });
    }

    public function translateModelField(Model $model, string $field, string $source, string $target, array $context = []): string
    {
        $original = (string) $model->getAttribute($field);
        $hash = hash('sha256', $original);

        $existing = AiTranslation::query()
            ->whereMorphedTo('translatable', $model)
            ->where('field', $field)
            ->where('source_locale', $source)
            ->where('target_locale', $target)
            ->where('source_hash', $hash)
            ->first();

        if ($existing) return $existing->translated_text;

        $translated = $this->translate($original, $source, $target, $context);

        AiTranslation::updateOrCreate([
            'translatable_type' => $model->getMorphClass(),
            'translatable_id' => $model->getKey(),
            'field' => $field,
            'source_locale' => $source,
            'target_locale' => $target,
            'source_hash' => $hash,
        ], [
            'original_text' => $original,
            'translated_text' => $translated,
            'provider' => config('ai.translation_provider'),
        ]);

        return $translated;
    }
}
