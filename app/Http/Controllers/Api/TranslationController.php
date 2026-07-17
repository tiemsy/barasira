<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\TranslateTextRequest;
use App\Services\Ai\TranslationService;
use Illuminate\Http\JsonResponse;
use Throwable;
class TranslationController extends Controller
{
    public function __invoke(TranslateTextRequest $request, TranslationService $translator): JsonResponse
    {
        try {
            $data = $request->validated();
            return response()->json([
                'translation' => $translator->translate($data['text'], $data['source_locale'], $data['target_locale'], $data['context'] ?? []),
                'source_locale' => $data['source_locale'],
                'target_locale' => $data['target_locale'],
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => app()->isProduction() ? 'La traduction est temporairement indisponible.' : $e->getMessage()], 422);
        }
    }
}
