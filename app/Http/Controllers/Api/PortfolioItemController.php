<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PortfolioItemStoreRequest;
use Illuminate\Http\JsonResponse;
use App\Models\PortfolioItem;
use App\Repositories\Eloquent\PortfolioItemRepositoryEloquent;
use OpenApi\Annotations as OA;

class PortfolioItemController extends Controller
{

    protected $portfolioItemRepository;

    public function __construct(PortfolioItemRepositoryEloquent $portfolioItemRepository)
    {
        $this->portfolioItemRepository = $portfolioItemRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->portfolioItemRepository->paginate(15, ['user'])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PortfolioItemStoreRequest $request): JsonResponse
    {
        $item = $this->portfolioItemRepository->create($request->validate());

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
