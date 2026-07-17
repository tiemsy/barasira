<?php

namespace App\Services\Ai;

use App\Models\Service;
use Illuminate\Support\Collection;

class MissionAiService
{
    public function __construct(private readonly AiManager $ai) {}

    public function generate(string $keywords, Collection $services): array
    {
        $catalog = $services->map(fn(Service $service) => [
            'id' => $service->id,
            'name' => $service->name,
        ])->values()->all();

        $system = 'Tu aides les utilisateurs de BaraSira à rédiger une mission au Mali. '
            . 'Réponds uniquement selon le schéma JSON. N’invente ni identité, ni téléphone, ni adresse précise. '
            . 'Choisis service_id uniquement parmi le catalogue fourni. Si aucun service ne correspond, utilise null.';

        $user = "Mots-clés : {$keywords}\nCatalogue des services : "
            . json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $result = $this->ai->generateJson($system, $user, $this->schema());
        $allowed = collect($catalog)->pluck('id')->map(fn($id) => (int) $id);

        $serviceId = isset($result['service_id']) ? (int) $result['service_id'] : null;
        if ($serviceId !== null && !$allowed->contains($serviceId)) $serviceId = null;

        return [
            'title' => trim((string) ($result['title'] ?? '')),
            'description' => trim((string) ($result['description'] ?? '')),
            'service_id' => $serviceId,
            'city' => trim((string) ($result['city'] ?? '')),
            'address' => trim((string) ($result['address'] ?? '')),
            'price' => isset($result['price']) ? max(0, (int) $result['price']) : null,
            'skills' => collect($result['skills'] ?? [])
                ->filter(static fn(mixed $skill): bool => is_string($skill))
                ->map(static fn(string $skill): string => trim($skill))
                ->filter()
                ->unique()
                ->take(10)
                ->values()
                ->all(),

            'questions' => collect($result['questions'] ?? [])
                ->filter(static fn(mixed $question): bool => is_string($question))
                ->map(static fn(string $question): string => trim($question))
                ->filter()
                ->unique()
                ->take(5)
                ->values()
                ->all(),
        ];
    }

    private function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'service_id' => ['type' => ['integer', 'null']],
                'city' => ['type' => 'string'],
                'address' => ['type' => 'string'],
                'price' => ['type' => ['integer', 'null']],
                'skills' => ['type' => 'array', 'items' => ['type' => 'string']],
                'questions' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
            'required' => [
                'title',
                'description',
                'service_id',
                'city',
                'address',
                'price',
                'skills',
                'questions'
            ],
            'additionalProperties' => false,
        ];
    }
}
