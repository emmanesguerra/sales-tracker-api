<?php

namespace App\Repositories\Sales;

use App\Models\SalesOrder;

class SalesRepository implements SalesRepositoryInterface
{
    public function create(array $data) : SalesOrder
    {
        return SalesOrder::create($data);
    }
}
