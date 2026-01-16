<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceCategory;
use App\Repositories\Interface\ServiceCategoryRepositoryInterface;

class ServiceCategoryRepositoryEloquent extends BaseRepositoryEloquent implements ServiceCategoryRepositoryInterface
{
    public function __construct(ServiceCategory $model)
    {
        parent::__construct($model);
    }

    public function randomServiceCategories()
    {
        return $this->model
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }
}
