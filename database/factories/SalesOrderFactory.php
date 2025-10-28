<?php

namespace Database\Factories;

use App\Models\SalesOrder;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tenant;

class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition()
    {
        $tenant = Tenant::inRandomOrder()->first() ?? Tenant::factory();
        $item = Item::factory()->create();

        return [
            'tenant_id' => $tenant->id,
            'order_date' => $this->faker->date(),
            'order_time' => $this->faker->time(),
            'item_id' => $item->id,
            'item_price' => $item->price,
            'quantity' => $this->faker->numberBetween(1, 10),
            'total_amount' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
