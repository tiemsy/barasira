<?php

namespace App\Repositories\Interface;

use App\Models\User;

interface MissionRepositoryInterface
{
    public function homeMissions();
    public function userMissions(User $user, array $filters);
}
