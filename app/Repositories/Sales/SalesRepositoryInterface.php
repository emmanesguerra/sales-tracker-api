<?php

namespace App\Repositories\Sales;

use App\Repositories\BaseRepositoryInterface;

interface SalesRepositoryInterface extends BaseRepositoryInterface
{
    public function findByOrderDate(string $date);
}
