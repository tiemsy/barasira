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
}
