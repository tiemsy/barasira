<?php

namespace App\Repositories\Eloquent;

use App\Models\Review;
use App\Repositories\Interface\ReviewRepositoryInterface;

class ReviewRepositoryEloquent extends BaseRepositoryEloquent implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }
}
