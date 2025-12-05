<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interface\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepositoryEloquent implements BaseRepositoryInterface
{
    protected Model $model;

    public function all(array $with = [])
    {
        return $this->model->with($with)->get();
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->model->with($with)->paginate($perPage);
    }

    public function find(int $id, array $with = [])
    {
        return $this->model->with($with)->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }
}
