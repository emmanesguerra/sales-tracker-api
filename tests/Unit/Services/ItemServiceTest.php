<?php

namespace Tests\Unit\Services;

use App\Repositories\Item\ItemRepositoryInterface;
use App\Services\Item\ItemService;
use Mockery;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    protected $itemService;
    protected $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(ItemRepositoryInterface::class);
        $this->itemService = new ItemService($this->repositoryMock);
    }

    /**
     * Helper function to mock repository methods
     */
    protected function mockRepositoryMethod($method, $arguments = [], $returnValue = null)
    {
        $this->repositoryMock->shouldReceive($method)
            ->withArgs($arguments)
            ->once()
            ->andReturn($returnValue);
    }

    public function test_can_get_all_items()
    {
        $this->mockRepositoryMethod('getAll', [], collect([]));
        
        $result = $this->itemService->getAllItems();
        $this->assertEmpty($result);
    }

    public function test_can_get_item_by_id()
    {
        $item = (object) ['id' => 1, 'name' => 'Test Item'];
        $this->mockRepositoryMethod('findById', [1], $item);

        $result = $this->itemService->getItemById(1);
        $this->assertEquals('Test Item', $result->name);
    }

    public function test_can_create_item()
    {
        $data = ['name' => 'New Item', 'price' => 100, 'stock' => 5];
        $this->mockRepositoryMethod('create', [$data], (object) $data);

        $result = $this->itemService->createItem($data);
        $this->assertEquals('New Item', $result->name);
    }

    public function test_can_update_item()
    {
        $data = ['name' => 'Updated Name'];
        $this->mockRepositoryMethod('update', [1, $data], (object) $data);

        $result = $this->itemService->updateItem(1, $data);
        $this->assertEquals('Updated Name', $result->name);
    }

    public function test_can_delete_item()
    {
        $this->mockRepositoryMethod('delete', [1], true);

        $result = $this->itemService->deleteItem(1);
        $this->assertTrue($result);
    }
}
