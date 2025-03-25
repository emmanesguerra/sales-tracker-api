<?php

namespace Tests\Unit\Repositories;

use App\Models\Item;
use App\Models\Tenant;
use App\Repositories\Item\ItemRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ItemRepository();
    }

    protected function createTenantWithItems($count = 3)
    {
        $tenant = Tenant::factory()->create();
        Item::factory()->count($count)->create([
            'tenant_id' => $tenant->id,
        ]);
        return $tenant;
    }

    public function test_can_get_all_items()
    {
        $this->createTenantWithItems(3);

        $items = $this->repository->getAll();

        $this->assertCount(3, $items);
    }

    public function test_can_find_item_by_id()
    {
        $tenant = $this->createTenantWithItems(1);
        $item = $tenant->items()->first();

        $foundItem = $this->repository->findById($item->id);

        $this->assertNotNull($foundItem);
        $this->assertEquals($item->id, $foundItem->id);
    }

    public function test_can_create_item()
    {
        $tenant = Tenant::factory()->create();

        $data = [
            'name' => 'Test Item',
            'code' => 'Item',
            'description' => 'Test Description',
            'price' => 100.50,
            'stock' => 10,
            'tenant_id' => $tenant->id,
        ];

        $item = $this->repository->create($data);

        $this->assertDatabaseHas('items', $data);
        $this->assertEquals('Test Item', $item->name);
    }

    public function test_can_update_item()
    {
        $tenant = $this->createTenantWithItems(1);
        $item = $tenant->items()->first();
        $data = ['name' => 'Updated Name'];

        $updatedItem = $this->repository->update($item->id, $data);

        $this->assertEquals('Updated Name', $updatedItem->name);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_item()
    {
        $tenant = $this->createTenantWithItems(1);
        $item = $tenant->items()->first();

        // Soft delete the item
        $this->repository->delete($item->id);

        // Assert that the item is soft deleted (it has a non-null deleted_at value)
        $softDeletedItem = Item::withTrashed()->find($item->id);
        $this->assertNotNull($softDeletedItem->deleted_at);
    }
}
