<?php

namespace App\Repositories\Interface;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email);
    public function search(string $query);
    public function missionProviders(User $user);
}
