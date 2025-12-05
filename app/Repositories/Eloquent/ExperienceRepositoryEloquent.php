<?php

namespace App\Repositories\Eloquent;

use App\Models\Experience;
use App\Repositories\Interface\ExperienceRepositoryInterface;

class ExperienceRepositoryEloquent extends BaseRepositoryEloquent implements ExperienceRepositoryInterface
{
    public function __construct(Experience $model)
    {
        parent::__construct($model);
    }
}
