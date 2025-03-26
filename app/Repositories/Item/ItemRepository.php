<?php

namespace App\Repositories\Item;

use App\Models\Item;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    public function __construct(Item $model)
    {
        parent::__construct($model);
    }

    public function findByArray(string $field, array $data)
    {
        return $this->findWhereIn($field, $data);
    }

    public function findOneRecordBy(string $field, string $data): ?Model
    {
        return $this->findByColumn($field, $data);
    }
}
