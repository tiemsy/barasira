<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interface\UserRepositoryInterface;

class UserRepositoryEloquent extends BaseRepositoryEloquent implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function search(string $query)
    {
        return $this->model
            ->where('first_name', 'LIKE', "%$query%")
            ->orWhere('last_name', 'LIKE', "%$query%")
            ->orWhere('email', 'LIKE', "%$query%")
            ->limit(20)
            ->get();
    }

    public function missionProviders($user)
    {
        return $this->model
            ->where('role', 'prestataire')
            ->whereHas('missionsAsPrestataire', function ($q) use ($user) {
                $q->where('client_id', $user->id());
            })
        ->get();
    }
}
