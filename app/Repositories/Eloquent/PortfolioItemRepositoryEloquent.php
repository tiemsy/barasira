<?php

namespace App\Repositories\Eloquent;

use App\Models\PortfolioItem;
use App\Repositories\Interface\PortfolioItemRepositoryInterface;

class PortfolioItemRepositoryEloquent extends BaseRepositoryEloquent implements PortfolioItemRepositoryInterface
{
    public function __construct(PortfolioItem $model)
    {
        parent::__construct($model);
    }
}
