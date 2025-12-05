<?php

namespace App\Repositories\Eloquent;

use App\Models\Dispute;
use App\Repositories\Interface\DisputeRepositoryInterface;

class DisputeRepositoryEloquent extends BaseRepositoryEloquent implements DisputeRepositoryInterface
{
    public function __construct(Dispute $model)
    {
        parent::__construct($model);
    }
}
