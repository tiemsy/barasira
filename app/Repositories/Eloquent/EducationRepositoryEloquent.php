<?php

namespace App\Repositories\Eloquent;

use App\Models\Education;
use App\Repositories\Interface\EducationRepositoryInterface;

class EducationRepositoryEloquent extends BaseRepositoryEloquent implements EducationRepositoryInterface
{
    public function __construct(Education $model)
    {
        parent::__construct($model);
    }
}
