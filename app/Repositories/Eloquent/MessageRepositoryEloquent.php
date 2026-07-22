<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Models\User;
use App\Repositories\Interface\MessageRepositoryInterface;
use Illuminate\Support\Collection;

class MessageRepositoryEloquent extends BaseRepositoryEloquent implements MessageRepositoryInterface
{
    public function __construct(Message $model)
    {
        parent::__construct($model);
    }

    public function conversationsFor(User $user): Collection
    {
        $baseQuery = $this->model->newQuery()
            ->where(fn ($query) => $query
                ->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id));

        $latestIds = (clone $baseQuery)
            ->selectRaw('MAX(id) as id')
            ->groupByRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END', [$user->id])
            ->groupBy('mission_id')
            ->pluck('id');

        if ($latestIds->isEmpty()) {
            return collect();
        }

        $unreadCounts = (clone $baseQuery)
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->selectRaw('sender_id, mission_id, COUNT(*) as total')
            ->groupBy('sender_id', 'mission_id')
            ->get()
            ->mapWithKeys(fn (Message $message) => [implode(':', [
                $message->sender_id,
                $message->mission_id ?? 'direct',
            ]) => (int) $message->total]);

        return $this->model->newQuery()
            ->whereKey($latestIds)
            ->with([
                'sender:id,first_name,last_name,avatar_url,role',
                'receiver:id,first_name,last_name,avatar_url,role',
                'mission:id,title,status',
            ])
            ->latest()
            ->get(['id', 'sender_id', 'receiver_id', 'mission_id', 'message', 'read', 'created_at'])
            ->map(function (Message $latest) use ($user, $unreadCounts) {
                $participant = $latest->sender_id === $user->id ? $latest->receiver : $latest->sender;
                $key = implode(':', [$participant->id, $latest->mission_id ?? 'direct']);

                return [
                    'participant' => $participant,
                    'mission' => $latest->mission,
                    'latest_message' => $latest,
                    'unread_count' => $unreadCounts->get($key, 0),
                ];
            });
    }

    public function conversation(User $user, User $participant, ?int $missionId = null): Collection
    {
        return $this->conversationQuery($user, $participant, $missionId)
            ->with('sender:id,first_name,last_name,avatar_url,role')
            ->oldest()
            ->get();
    }

    public function markConversationAsRead(User $user, User $participant, ?int $missionId = null): int
    {
        return $this->conversationQuery($user, $participant, $missionId)
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);
    }

    public function unreadCountFor(User $user): int
    {
        return $this->model->newQuery()
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->count();
    }

    private function conversationQuery(User $user, User $participant, ?int $missionId)
    {
        return $this->model->newQuery()
            ->where(function ($query) use ($user, $participant) {
                $query
                    ->where(fn ($pair) => $pair
                        ->where('sender_id', $user->id)
                        ->where('receiver_id', $participant->id))
                    ->orWhere(fn ($pair) => $pair
                        ->where('sender_id', $participant->id)
                        ->where('receiver_id', $user->id));
            })
            ->when(
                $missionId,
                fn ($query) => $query->where('mission_id', $missionId),
                fn ($query) => $query->whereNull('mission_id')
            );
    }
}
