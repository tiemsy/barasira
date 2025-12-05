<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ServiceStoreRequest;
use App\Http\Requests\ServiceUpdateRequest;
use OpenApi\Annotations as OA;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $services = Service::with(['category', 'userSkills'])->paginate(20);
        return response()->json($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceStoreRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());
        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): JsonResponse
    {
        $service->load(['category', 'userSkills']);
        return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceUpdateRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());
        return response()->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): JsonResponse
    {
        $service->delete();
        return response()->json(null, 204);
    }
}
