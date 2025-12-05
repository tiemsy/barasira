<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Interface\PaymentRepositoryInterface;

class PaymentRepositoryEloquent extends BaseRepositoryEloquent implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }
}
