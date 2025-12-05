<?php

namespace App\Repositories\Eloquent;

use App\Models\Certification;
use App\Repositories\Interface\CertificationRepositoryInterface;

class CertificationRepositoryEloquent extends BaseRepositoryEloquent implements CertificationRepositoryInterface
{
    public function __construct(Certification $model)
    {
        parent::__construct($model);
    }
}
