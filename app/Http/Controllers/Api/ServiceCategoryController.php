<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ServiceCategoryStoreRequest;
use App\Http\Requests\ServiceCategoryUpdateRequest;
use OpenApi\Annotations as OA;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(ServiceCategory::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceCategoryStoreRequest $request): JsonResponse
    {
        $category = ServiceCategory::create($request->validated());
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCategory $serviceCategory): JsonResponse
    {
        return response()->json($serviceCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceCategoryUpdateRequest $request, ServiceCategory $serviceCategory): JsonResponse
    {
        $serviceCategory->update($request->validated());
        return response()->json($serviceCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $serviceCategory): JsonResponse
    {
        $serviceCategory->delete();
        return response()->json(null, 204);
    }
}
