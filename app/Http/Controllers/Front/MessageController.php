<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Messages/Index', [
            'initialUserId' => $request->integer('user') ?: null,
            'initialMissionId' => $request->integer('mission') ?: null,
        ]);
    }

    public function create(Request $request): Response
    {
        $recipient = $request->integer('user')
            ? User::query()->select(['id', 'first_name', 'last_name', 'avatar_url', 'role'])->find($request->integer('user'))
            : null;

        $mission = $request->integer('mission')
            ? Mission::query()->select(['id', 'title', 'status'])->find($request->integer('mission'))
            : null;

        return Inertia::render('Messages/Create', [
            'recipient' => $recipient,
            'mission' => $mission,
        ]);
    }
}
