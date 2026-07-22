<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceStoreRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Models\Service;
use App\Repositories\Eloquent\ServiceRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="Service",
 * type="object",
 * title="Service",
 * required={"id", "slug", "name"},
 *
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="slug", type="string", example="plomberie"),
 * @OA\Property(property="user_id", type="integer", example="1"),
 * @OA\Property(property="service_category_id", type="integer", example="2"),
 * @OA\Property(property="city_id", type="integer", example="1"),
 * @OA\Property(property="municipality_id", type="integer", example="3"),
 * @OA\Property(property="name", type="string", example="Plomberie"),
 * @OA\Property(property="description", type="text", example="description du service"),
 * @OA\Property(property="icon", type="string", example="icône ou pictogramme optionnel"),
 * @OA\Property(property="price_min", type="integer", example="1000"),
 * @OA\Property(property="price_max", type="integer", example="50000"),
 * @OA\Property(property="is_active", type="boolean", example="true")
 * )
 */
class ServiceController extends Controller
{
    protected $serviceRepository;

    public function __construct(ServiceRepositoryEloquent $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/services",
     *     tags={"Services"},
     *     summary="Liste des services",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Liste des services",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Service"))
     *     )
     * )
     *
     * @param  Request  $request
     */
    public function index(): JsonResponse
    {
        $services = Service::query()
            ->where('is_active', true)
            ->select(['id', 'slug', 'name', 'service_category_id', 'user_id'])
            ->orderBy('name')
            ->get();

        return response()->json($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Service::class);
        $service = $this->serviceRepository->create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): JsonResponse
    {
        $service->load(['category', 'city', 'municipality', 'missions', 'user']);

        return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceUpdateRequest $request, Service $service): JsonResponse
    {
        $this->authorize('update', $service);
        $service->update($request->validated());

        return response()->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Service $service): JsonResponse
    {
        $this->authorize('delete', $service);

        if ($service->missions()->exists()) {
            return response()->json([
                'message' => __('Ce service est lié à une mission et ne peut pas être supprimé.'),
            ], 422);
        }

        $service->delete();

        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $services = Service::with(['city', 'municipality', 'category', 'user'])
            ->where('is_active', true)
            ->when(
                $request->keyword,
                fn ($query) => $query->where(function ($search) use ($request) {
                    $search
                        ->where('name', 'like', "%{$request->keyword}%")
                        ->orWhere('description', 'like', "%{$request->keyword}%");
                })
            )
            ->when(
                $request->city,
                fn ($query) => $query->where('city_id', $request->city)
            )
            ->when(
                $request->category,
                fn ($query) => $query->where('service_category_id', $request->category)
            )
            ->when(
                $request->sort === 'price_asc',
                fn ($query) => $query->orderBy('price_min'),
                fn ($query) => $query->latest()
            )
            ->paginate(12)
            ->withQueryString();

        return response()->json($services);
    }
}
