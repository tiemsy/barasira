<?php

namespace App\Repositories\Interface;

interface MissionRepositoryInterface
{
    public function homeMissions();
    public function userMissions($user);
}
