<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageStoreRequest;
use App\Models\Message;
use App\Models\Mission;
use App\Models\User;
use App\Repositories\Eloquent\MessageRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function __construct(
        private readonly MessageRepositoryEloquent $messageRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'conversations' => $this->messageRepository->conversationsFor($request->user()),
            'unread_count' => $this->messageRepository->unreadCountFor($request->user()),
        ]);
    }

    public function conversation(Request $request, User $user): JsonResponse
    {
        $missionId = $request->integer('mission_id') ?: null;
        $this->ensureUsersCanMessage($request->user(), $user, $missionId);

        $messages = $this->messageRepository->conversation(
            $request->user(),
            $user,
            $missionId
        );

        $this->messageRepository->markConversationAsRead(
            $request->user(),
            $user,
            $missionId
        );

        return response()->json([
            'participant' => $user->only(['id', 'first_name', 'last_name', 'avatar_url', 'role']),
            'mission' => $missionId
                ? Mission::query()->select(['id', 'title', 'status'])->find($missionId)
                : null,
            'messages' => $messages,
        ]);
    }

    public function store(MessageStoreRequest $request): JsonResponse
    {
        $receiver = User::findOrFail($request->integer('receiver_id'));
        $missionId = $request->integer('mission_id') ?: null;

        $this->ensureUsersCanMessage($request->user(), $receiver, $missionId, true);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $receiver->id,
            'mission_id' => $missionId,
            'message' => trim($request->string('message')->toString()),
            'read' => false,
        ])->load(['sender:id,first_name,last_name,avatar_url,role']);

        return response()->json([
            'message' => $message,
        ], 201);
    }

    private function ensureUsersCanMessage(User $sender, User $receiver, ?int $missionId, bool $writing = false): void
    {
        if ($sender->is($receiver)) {
            throw ValidationException::withMessages([
                'receiver_id' => 'Vous ne pouvez pas vous envoyer un message à vous-même.',
            ]);
        }

        if ($missionId) {
            $this->ensureMissionAllowsMessaging($sender, $receiver, $missionId, $writing);

            return;
        }

        $roles = [$sender->role, $receiver->role];
        sort($roles);

        if ($roles !== ['client', 'prestataire']) {
            throw ValidationException::withMessages([
                'receiver_id' => 'Les messages sont réservés aux échanges entre clients et prestataires.',
            ]);
        }
    }

    private function ensureMissionAllowsMessaging(User $sender, User $receiver, int $missionId, bool $writing): void
    {
        $mission = Mission::query()->findOrFail($missionId);
        $participants = collect([$sender->id, $receiver->id]);
        $isClientInConversation = $participants->contains($mission->client_id);
        $providerId = $participants->first(fn (int $id) => $id !== $mission->client_id);

        $hasApplication = $mission->applications()
            ->where('worker_id', $providerId)
            ->exists();

        if (! $isClientInConversation
            || ($mission->prestataire_id !== $providerId && ! $hasApplication)) {
            throw ValidationException::withMessages([
                'mission_id' => 'Vous ne pouvez pas échanger au sujet de cette mission.',
            ]);
        }

        if ($writing && $mission->status === 'completed') {
            throw ValidationException::withMessages([
                'mission_id' => 'Cette mission est terminée. Aucun nouveau message ne peut être envoyé.',
            ]);
        }
    }
}
