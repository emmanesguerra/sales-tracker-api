<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): void;

    public function findByColumn(string $column, string|int $value): ?Model;

    public function findAllByColumn(string $column, string|int $value): Collection;

    public function findWhereIn(string $column, array $values): Collection;

    public function exists(string $column, string|int $value): bool;

    public function paginate(int $perPage = 10);
}
