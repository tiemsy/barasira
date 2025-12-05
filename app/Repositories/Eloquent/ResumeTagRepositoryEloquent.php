<?php

namespace App\Repositories\Eloquent;

use App\Models\ResumeTag;
use App\Repositories\Interface\ResumeTagRepositoryInterface;

class ResumeTagRepositoryEloquent extends BaseRepositoryEloquent implements ResumeTagRepositoryInterface
{
    public function __construct(ResumeTag $model)
    {
        parent::__construct($model);
    }
}
