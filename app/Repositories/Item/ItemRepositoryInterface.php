<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

interface ItemRepositoryInterface extends BaseRepositoryInterface
{
    public function findByArray(string $field, array $data);

    public function findOneRecordBy(string $field, string $data): ?Model;
}
