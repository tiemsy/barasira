<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Interface\NotificationRepositoryInterface;

class NotificationRepositoryEloquent extends BaseRepositoryEloquent implements NotificationRepositoryInterface
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }
}
