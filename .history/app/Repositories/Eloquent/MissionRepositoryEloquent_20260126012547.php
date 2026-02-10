<?php

namespace App\Repositories\Eloquent;

use App\Models\Mission;
use App\Repositories\Interface\MissionRepositoryInterface;

class MissionRepositoryEloquent extends BaseRepositoryEloquent implements MissionRepositoryInterface
{
    public function __construct(Mission $model)
    {
        parent::__construct($model);
    }

    public function homeMissions()
    {
        return $this->model
            ->limit(4)
            ->get();
    }


    public function userMissions($user)
    {
        return $this->model->where('client_id', $user->id)->with('prestataire')->get();
    }
}
