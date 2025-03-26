<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): void
    {
        $record = $this->model->findOrFail($id);
        $record->delete();
    }

    public function findByColumn(string $column, string|int $value): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    public function findAllByColumn(string $column, string|int $value): Collection
    {
        return $this->model->where($column, $value)->get();
    }

    public function findWhereIn(string $column, array $values): Collection
    {
        return $this->model->whereIn($column, $values)->get();
    }

    public function exists(string $column, string|int $value): bool
    {
        return $this->model->where($column, $value)->exists();
    }

    public function paginate(int $perPage = 10)
    {
        return $this->model->paginate($perPage);
    }
}
