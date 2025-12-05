<?php

namespace App\Repositories\Eloquent;

use App\Models\Favorite;
use App\Repositories\Interface\FavoriteRepositoryInterface;

class FavoriteRepositoryEloquent extends BaseRepositoryEloquent implements FavoriteRepositoryInterface
{
    public function __construct(Favorite $model)
    {
        parent::__construct($model);
    }
}
