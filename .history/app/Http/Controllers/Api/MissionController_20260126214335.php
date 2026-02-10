<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MissionStoreRequest;
use App\Http\Requests\MissionUpdateRequest;
use App\Models\Mission;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="Mission",
 * type="object",
 * title="Mission",
 * required={"id", "name"},
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="client_id", type="integer", example="1"),
 * @OA\Property(property="service_id", type="integer", example="2"),
 * @OA\Property(property="title", type="string", example="Besoin jardinage"),
 * @OA\Property(property="description", type="text", example="description de la mission"),
 * @OA\Property(property="address", type="string", example="Adresse de la mission"),
 * @OA\Property(property="price", type="integer", example="50000"),
 * @OA\Property(property="date_start", type="dateTime", example=""),
 * @OA\Property(property="data_end", type="dateTime", example=""),
 * @OA\Property(property="status", type="boolean", example="pending, in_progress...")
 * )
 */
class MissionController extends Controller
{
    protected $missionRepository;

    public function __construct(MissionRepositoryEloquent $missionRepository)
    {
        $this->missionRepository = $missionRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/missions",
     *     tags={"Missions"},
     *     summary="Liste des missions",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des missions",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Mission"))
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $missions = $this->missionRepository->paginate(5);
        return response()->json($missions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MissionStoreRequest $request): JsonResponse
    {
        $mission = $this->missionRepository->create($request->validated());
        return response()->json($mission, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mission $mission): JsonResponse
    {
        $mission->load(['client', 'service', 'applications', 'payments', 'reviews', 'messages']);
        return response()->json($mission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MissionUpdateRequest $request, Mission $mission): JsonResponse
    {
        $mission->update($request->validated());
        return response()->json($mission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mission $mission): JsonResponse
    {
        $mission->delete();
        return response()->json(null, 204);
    }
}
