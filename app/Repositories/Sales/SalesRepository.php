<?php

namespace App\Repositories\Sales;

use App\Models\SalesOrder;
use App\Repositories\BaseRepository;

class SalesRepository extends BaseRepository implements SalesRepositoryInterface
{
    public function __construct(SalesOrder $model)
    {
        parent::__construct($model);
    }

    public function findByOrderDate(string $date)
    {
        return $this->findAllByColumn('order_date', $date);
    }
}
