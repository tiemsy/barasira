<?php

namespace App\Repositories\Eloquent;

use App\Models\City;
use App\Repositories\Interface\CityRepositoryInterface;

class CityRepositoryEloquent extends BaseRepositoryEloquent  implements CityRepositoryInterface
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }
}
