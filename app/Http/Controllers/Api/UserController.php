<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Repositories\Eloquent\UserRepositoryEloquent;
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
 * @OA\Property(property="rating", type="decimal", example="4.5"),
 * @OA\Property(property="verified", type="boolean", example="true")
 * )
 */
class UserController extends Controller
{
    protected $userRepository;

    /**
     * __construct function
     *
     * @param UserRepositoryEloquent $userRepository
     */
    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Liste des utilisateurs",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs avec les services liés",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->userRepository->paginate(15, ['services'])
        );
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Créer un utilisateur",
     *     @OA\Parameter(
     *         name="first_name",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="password",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="path",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="bio",
     *         in="path",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="avatar_url",
     *         in="path",
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="rating",
     *         in="path",
     *         @OA\Schema(type="decimal")
     *     ),
     *      @OA\Parameter(
     *         name="verified",
     *         in="path",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"first_name", "last_name", "email", "password"},
     *              @OA\Property(property="first_name", type="string", example="Jean"),
     *              @OA\Property(property="last_name", type="string", example="Dupont"),
     *              @OA\Property(property="email", type="string", example="jean@example.com"),
     *              @OA\Property(property="password", type="string", example="******"),
     *              @OA\Property(property="phone", type="string", example="0022374321256"),
     *              @OA\Property(property="role", type="enum", example="client"),
     *              @OA\Property(property="bio", type="text", example="Biography"),
     *              @OA\Property(property="avatar_url", type="string", example="/storage/john/doe/jeandupont.jpg"),
     *              @OA\Property(property="rating", type="decimal", example="4.5"),
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

        $user = $this->userRepository->create($data);

        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Obtenir un utilisateur par ID",
     *     @OA\Parameter(
     *         name="user",
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
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['services']);
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Mise à jour d’un utilisateur par ID",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="path",
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="first_name", type="string", example="jean"),
     *              @OA\Property(property="last_name", type="string", example="Dupont"),
     *              @OA\Property(property="email", type="string", example="jean@example.com"),
     *              @OA\Property(property="password", type="string", example="******"),
     *              @OA\Property(property="phone", type="string", example="0022374321256"),
     *              @OA\Property(property="role", type="enum", example="client"),
     *              @OA\Property(property="bio", type="text", example="Biography"),
     *              @OA\Property(property="avatar_url", type="string", example="/storage/john/doe/jeandupont.jpg"),
     *              @OA\Property(property="rating", type="decimal", example="4.5"),
     *              @OA\Property(property="verified", type="boolean", example="true")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur modifié",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=404, description="Utilisateur introuvable")
     * )
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        // var_dump($user); die;
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
    /**
     * @OA\Delete(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Suppression d’un utilisateur par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur supprimé",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=404, description="Utilisateur introuvable")
     * )
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        var_dump($user); die;
        $user->delete();

        return response()->json(null, 204);
    }
}
