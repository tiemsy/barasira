<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MissionScheduleConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\MissionStoreRequest;
use App\Http\Requests\MissionUpdateRequest;
use App\Models\Mission;
use App\Repositories\Eloquent\MissionRepositoryEloquent;
use App\Services\MissionAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="Mission",
 * type="object",
 * title="Mission",
 * required={"id", "name"},
 *
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

    public function __construct(
        MissionRepositoryEloquent $missionRepository,
        private readonly MissionAssignmentService $missionAssignmentService
    ) {
        $this->missionRepository = $missionRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/missions",
     *     tags={"Missions"},
     *     summary="Liste des missions",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Liste des missions",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Mission"))
     *     )
     * )
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
        $data = $request->validated();
        $mission = $this->missionRepository->create($data + [
            'client_id' => $request->user()->id,
            'prestataire_id' => null,
            'status' => 'pending',
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
    public function show(Request $request, Mission $mission): JsonResponse
    {
        $this->authorize('view', $mission);

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

    public function claim(Request $request, Mission $mission): JsonResponse
    {
        $this->authorize('claim', $mission);

        try {
            $mission = $this->missionAssignmentService->claim($mission, $request->user());
        } catch (MissionScheduleConflictException $exception) {
            return response()->json([
                'success' => false,
                'code' => 'MISSION_SCHEDULE_CONFLICT',
                'message' => $exception->getMessage(),
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => __('La mission vous a été attribuée.'),
            'data' => $mission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MissionUpdateRequest $request, Mission $mission): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if (! $user->isAdmin()) {
            if (array_key_exists('status', $data)) {
                $this->ensureAllowedStatusTransition($mission, $data['status'], $user->id);
            }

            $isOwner = $mission->client_id === $user->id;
            $changesDetails = count(array_diff(array_keys($data), ['status'])) > 0;

            if ($changesDetails && (! $isOwner || $mission->status !== 'pending')) {
                throw ValidationException::withMessages([
                    'mission' => 'Les détails ne peuvent être modifiés que par le client tant que la mission est en attente.',
                ]);
            }
        }

        $mission->update($data);

        return response()->json([
            'success' => true,
            'message' => 'La mission a été mise à jour avec succès.',
            'data' => $mission->fresh(['prestataire', 'service.category']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Mission $mission): JsonResponse
    {
        $this->authorize('delete', $mission);
        $mission->delete();

        return response()->json(null, 204);
    }

    private function ensureAllowedStatusTransition(Mission $mission, string $status, int $userId): void
    {
        if ($status === $mission->status) {
            return;
        }

        if ($status === 'cancelled' && $mission->payments()->where('status', 'en_attente')->exists()) {
            throw ValidationException::withMessages([
                'status' => 'Cette mission ne peut plus être annulée pendant la confirmation du paiement.',
            ]);
        }

        $allowed = match (true) {
            $mission->client_id === $userId => [
                'pending' => ['cancelled'],
                'in_progress' => ['cancelled'],
            ][$mission->status] ?? [],
            // Une mission n'est terminée qu'après validation et paiement par le client.
            $mission->prestataire_id === $userId => [],
            default => [],
        };

        if (! in_array($status, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => 'Cette transition de statut n’est pas autorisée.',
            ]);
        }
    }
}
