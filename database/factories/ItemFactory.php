<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $tenant = Tenant::inRandomOrder()->first() ?? Tenant::factory();

        return [
            'tenant_id' => $tenant->id,
            'code' => $this->faker->word(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1, 1000), // Price between 1 and 1000
            'stock' => $this->faker->numberBetween(0, 100), // Stock between 0 and 100
            'created_by' => User::factory(), // Generate a new user
            'updated_by' => null, // Initially null, gets updated later
            'deleted_by' => null, // Initially null, only used when deleted
        ];
    }
}
