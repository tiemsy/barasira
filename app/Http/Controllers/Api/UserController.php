<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="User",
 * type="object",
 * title="User",
 * required={"id", "name", "email"},
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="first_name", type="string", example="John"),
 * @OA\Property(property="last_name", type="string", example="Doe"),
 * @OA\Property(property="email", type="string", example="john@example.com"),
 * @OA\Property(property="password", type="string", example="******"),
 * @OA\Property(property="phone", type="string", example="0022374321256"),
 * @OA\Property(property="role", type="enum", example="['client', 'prestataire', 'admin'], Defauft:client"),
 * @OA\Property(property="bio", type="text", example="Biography"),
 * @OA\Property(property="avatar_url", type="string", example="/storage/john/doe/johndoe.jpg"),
 * @OA\Property(property="rating", type="decimal", example="100.00"),
 * @OA\Property(property="verified", type="boolean", example="true")
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Liste des utilisateurs",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $users = User::with(['services'])->paginate($perPage);

        return response()->json($users);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Créer un utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"name", "email"},
     *              @OA\Property(property="first_name", type="string", example="Jean"),
     *              @OA\Property(property="last_name", type="string", example="Dupont"),
     *              @OA\Property(property="email", type="string", example="jean@example.com"),
     *              @OA\Property(property="password", type="string", example="******"),
     *              @OA\Property(property="phone", type="string", example="0022374321256"),
     *              @OA\Property(property="role", type="enum", example="['client', 'prestataire', 'admin'], Defauft:client"),
     *              @OA\Property(property="bio", type="text", example="Biography"),
     *              @OA\Property(property="avatar_url", type="string", example="/storage/john/doe/jeandupont.jpg"),
     *              @OA\Property(property="rating", type="decimal", example="100.00"),
     *              @OA\Property(property="verified", type="boolean", example="true")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     *
     * @param UserStoreRequest $request
     * @return JsonResponse
     */

    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Hasher le mot de passe
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Obtenir un utilisateur par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur trouvé",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=404, description="Utilisateur introuvable")
     * )
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['services']);
        return response()->json($user);
    }

    /**
     * Mise à jour d’un utilisateur
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('password', $data) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            // On évite d'écraser le mot de passe si non fourni
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    /**
     * Suppression d’un utilisateur.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
