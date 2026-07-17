<?php

namespace App\Repositories\Interface;

use App\Models\User;

interface MessageRepositoryInterface
{
    public function conversationsFor(User $user);

    public function conversation(User $user, User $participant, ?int $missionId = null);

    public function markConversationAsRead(User $user, User $participant, ?int $missionId = null): int;

    public function unreadCountFor(User $user): int;
}
