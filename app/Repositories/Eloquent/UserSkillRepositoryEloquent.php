<?php

namespace App\Repositories\Eloquent;

use App\Models\UserSkill;
use App\Repositories\Interface\UserSkillRepositoryInterface;

class UserSkillRepositoryEloquent extends BaseRepositoryEloquent implements UserSkillRepositoryInterface
{
    public function __construct(UserSkill $model)
    {
        parent::__construct($model);
    }
}
