<?php

namespace App\Repositories\Eloquent;

use App\Models\Document;
use App\Repositories\Interface\DocumentRepositoryInterface;

class DocumentRepositoryEloquent extends BaseRepositoryEloquent implements DocumentRepositoryInterface
{
    public function __construct(Document $model)
    {
        parent::__construct($model);
    }
}
