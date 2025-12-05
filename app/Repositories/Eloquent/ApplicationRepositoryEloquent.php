<?php

namespace App\Repositories\Eloquent;

use App\Models\Application;
use App\Repositories\Interface\ApplicationRepositoryInterface;

class ApplicationRepositoryEloquent extends BaseRepositoryEloquent implements ApplicationRepositoryInterface
{
    public function __construct(Application $model){
        parent::__construct($model);
    }
}
