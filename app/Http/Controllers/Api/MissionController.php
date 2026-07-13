<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MissionStoreRequest;
use App\Http\Requests\MissionUpdateRequest;
use App\Models\Mission;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $missions = $this->missionRepository->userMissions($user, $request->all());
        return response()->json($missions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MissionStoreRequest $request): JsonResponse
    {
        // $mission = $this->missionRepository->create($request->validated());
        // return response()->json($mission, 201);

        $data = $request->validated();

        $mission = Mission::create([
            'client_id' => $request->user()->id,
            'prestataire_id' => null,
            'service_id' => $data['service_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'status' => 'pending',
            'price' => $data['price'] ?? null,
            'date_start' => $data['date_start'] ?? null,
            'date_end' => $data['date_end'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'La mission a été créée avec succès.',
            'data' => $mission,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mission $mission): JsonResponse
    {
        $mission->load([
            'client',
            'prestataire',
            'service.category',
            'applications',
            'payments',
            'reviews',
            'messages',
        ]);

        return response()->json([
            'success' => true,
            'mission' => $mission,
        ]);
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
