<?php

namespace App\Repositories\Eloquent;

use App\Models\Resume;
use App\Repositories\Interface\ResumeRepositoryInterface;

class ResumeRepositoryEloquent extends BaseRepositoryEloquent implements ResumeRepositoryInterface
{
    public function __construct(Resume $model)
    {
        parent::__construct($model);
    }
}
