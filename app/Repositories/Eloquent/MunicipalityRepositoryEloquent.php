<?php

namespace App\Repositories\Eloquent;

use App\Models\Municipality;
use App\Repositories\Interface\MunicipalityRepositoryInterface;

class MunicipalityRepositoryEloquent extends BaseRepositoryEloquent  implements MunicipalityRepositoryInterface
{
    public function __construct(Municipality $model)
    {
        parent::__construct($model);
    }
}
