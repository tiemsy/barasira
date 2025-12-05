<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Interface\MessageRepositoryInterface;

class MessageRepositoryEloquent extends BaseRepositoryEloquent implements MessageRepositoryInterface
{
    public function __construct(Message $model)
    {
        parent::__construct($model);
    }
}
