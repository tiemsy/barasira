<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PortfolioItemStoreRequest;
use Illuminate\Http\JsonResponse;
use App\Models\PortfolioItem;
use OpenApi\Annotations as OA;

class PortfolioItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            PortfolioItem::with(['user'])->paginate(15)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PortfolioItemStoreRequest $request): JsonResponse
    {
        $item = PortfolioItem::create($request->validate());

        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PortfolioItem $portfolioItem): JsonResponse
    {
        $portfolioItem->load(['user']);
        return response()->json($portfolioItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PortfolioItem $portfolioItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PortfolioItem $portfolioItem): JsonResponse
    {
        $portfolioItem->delete();
        return response()->json(null, 204);
    }
}
