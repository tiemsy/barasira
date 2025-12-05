<?php

namespace App\Repositories\Eloquent;

use App\Models\ResumeLanguage;
use App\Repositories\Interface\ResumeLanguageRepositoryInterface;

class ResumeLanguageRepositoryEloquent extends BaseRepositoryEloquent implements ResumeLanguageRepositoryInterface
{
    public function __construct(ResumeLanguage $model)
    {
        parent::__construct($model);
    }
}
