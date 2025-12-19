<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserSkillStoreRequest;
use Illuminate\Http\Request;
use App\Models\UserSkill;
use App\Repositories\Eloquent\UserSkillRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class UserSkillController extends Controller
{
    protected $userSkillRepository;

    public function __construct(UserSkillRepositoryEloquent $userSkillRepository)
    {
        $this->userSkillRepository = $userSkillRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->userSkillRepository->all(['user', 'service']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserSkillStoreRequest $request): JsonResponse
    {
        $skill = UserSkill::create($request->validated());
        return response()->json($skill, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserSkill $userSkill)
    {
        $userSkill->load(['user', 'service']);
        return response()->json($userSkill);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserSkill $userSkill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserSkill $userSkill): JsonResponse
    {
        $userSkill->delete();
        return response()->json(null, 204);
    }
}
