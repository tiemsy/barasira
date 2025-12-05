<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Interface\ServiceRepositoryInterface;

class ServiceRepositoryEloquent extends BaseRepositoryEloquent implements ServiceRepositoryInterface
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }
}
