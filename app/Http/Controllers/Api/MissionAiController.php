<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateMissionAiRequest;
use App\Models\Service;
use App\Services\Ai\MissionAiService;
use Illuminate\Http\JsonResponse;
use Throwable;
class MissionAiController extends Controller
{
    public function __invoke(GenerateMissionAiRequest $request, MissionAiService $service): JsonResponse
    {
        try {
            $services = Service::query()->select('id','name')->orderBy('name')->get();
            return response()->json(['mission' => $service->generate($request->validated('keywords'), $services)]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => app()->isProduction() ? 'Le service IA est temporairement indisponible.' : $e->getMessage()], 422);
        }
    }
}
