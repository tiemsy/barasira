<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentationController extends Controller
{
    /**
     * Only these repository files may be exposed through the administration.
     *
     * @var array<string, array{title: string, description: string, type: string, path: string}>
     */
    private const DOCUMENTS = [
        'technical-specifications' => [
            'title' => 'Spécifications techniques',
            'description' => 'Architecture, API, données, sécurité, intégrations et exploitation.',
            'type' => 'pdf',
            'path' => 'docs/Specifications-Techniques-BaraSira.pdf',
        ],
        'business-model' => [
            'title' => 'Business Model',
            'description' => 'Modèle économique, stratégie, financement et prévisions.',
            'type' => 'pdf',
            'path' => 'docs/Business-Model-BaraSira.pdf',
        ],
        'readme' => [
            'title' => 'README développeur',
            'description' => 'Installation, fonctionnalités, commandes et conventions du projet.',
            'type' => 'text',
            'path' => 'README.md',
        ],
        'technical-source' => [
            'title' => 'Source des spécifications',
            'description' => 'Version Markdown maintenable des spécifications techniques.',
            'type' => 'text',
            'path' => 'docs/SPECIFICATIONS-TECHNIQUES.md',
        ],
        'openapi-yaml' => [
            'title' => 'OpenAPI YAML',
            'description' => 'Contrat versionnable de l’API REST BaraSira.',
            'type' => 'code',
            'path' => 'storage/api-docs/api-docs.yaml',
        ],
        'openapi-json' => [
            'title' => 'OpenAPI JSON',
            'description' => 'Contrat OpenAPI utilisé par Swagger UI.',
            'type' => 'code',
            'path' => 'storage/api-docs/api-docs.json',
        ],
    ];

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $documents = collect(self::DOCUMENTS)
            ->map(function (array $document, string $key): array {
                $path = base_path($document['path']);

                return [
                    'key' => $key,
                    'title' => $document['title'],
                    'description' => $document['description'],
                    'type' => $document['type'],
                    'available' => is_file($path),
                    'size' => is_file($path) ? filesize($path) : null,
                    'updated_at' => is_file($path) ? filemtime($path) : null,
                    'view_url' => route('admin.documentation.file', ['document' => $key]),
                    'download_url' => route('admin.documentation.file', ['document' => $key, 'download' => 1]),
                ];
            })
            ->values();

        return Inertia::render('Admin/Documentation/Index', [
            'documents' => $documents,
            'swaggerUrl' => route('l5-swagger.default.api'),
        ]);
    }

    public function file(Request $request, string $document): BinaryFileResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $definition = self::DOCUMENTS[$document] ?? null;
        abort_unless($definition !== null, 404);

        $path = base_path($definition['path']);
        abort_unless(is_file($path), 404);

        if ($request->boolean('download')) {
            return response()->download($path, basename($definition['path']));
        }

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="'.basename($definition['path']).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
