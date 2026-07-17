<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

class GenerateSwaggerDocumentation extends Command
{
    protected $signature = 'swagger:generate {--check : Validate without writing generated files}';

    protected $description = 'Generate L5-Swagger annotations and validate them against API routes';

    public function handle(Router $router): int
    {
        if ($this->call('l5-swagger:generate') !== self::SUCCESS) {
            return self::FAILURE;
        }

        $document = json_decode(
            file_get_contents(storage_path('api-docs/api-docs.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $documented = $this->documentedOperations($document['paths'] ?? []);
        $routed = $this->apiOperations($router);
        $missing = array_values(array_diff($routed, $documented));
        $obsolete = array_values(array_diff($documented, $routed));

        if ($missing !== [] || $obsolete !== []) {
            $this->reportDifferences('Routes API absentes de la documentation', $missing);
            $this->reportDifferences('Opérations documentées sans route API', $obsolete);

            return self::FAILURE;
        }

        $this->info(sprintf('Documentation Swagger synchronisée : %d opérations.', count($routed)));

        return self::SUCCESS;
    }

    private function apiOperations(Router $router): array
    {
        return collect($router->getRoutes()->getRoutes())
            ->filter(fn (Route $route) => str_starts_with($route->uri(), 'api/'))
            ->reject(fn (Route $route) => in_array($route->uri(), ['api/documentation', 'api/docs', 'api/oauth2-callback', 'api/debug-auth'], true))
            ->flatMap(fn (Route $route) => collect($route->methods())
                ->reject(fn (string $method) => $method === 'HEAD')
                ->map(fn (string $method) => strtolower($method).' /'.$this->normalizePath($route->uri())))
            ->unique()->sort()->values()->all();
    }

    private function documentedOperations(array $paths): array
    {
        $methods = ['get', 'post', 'put', 'patch', 'delete', 'options'];

        return collect($paths)->flatMap(function (array $operations, string $path) use ($methods) {
            return collect(Arr::only($operations, $methods))
                ->keys()
                ->map(fn (string $method) => $method.' /'.$this->normalizePath(ltrim($path, '/')));
        })->unique()->sort()->values()->all();
    }

    private function normalizePath(string $path): string
    {
        return preg_replace('/\{[^}]+}/', '{}', $path);
    }

    private function reportDifferences(string $title, array $operations): void
    {
        if ($operations === []) {
            return;
        }

        $this->error($title.':');
        foreach ($operations as $operation) {
            $this->line('  - '.$operation);
        }
    }
}
