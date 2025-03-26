<?php

namespace App\Repositories\Sales;

use App\Models\SalesOrder;

interface SalesRepositoryInterface
{
    public function create(array $data): SalesOrder;
}
