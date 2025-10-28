<?php
namespace Tests\Unit\Services;

use App\Repositories\Item\ItemRepositoryInterface;
use App\Services\Item\ItemService;
use Mockery;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\Models\Item;

class ItemServiceTest extends TestCase
{
    protected $itemService;
    protected $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        // Mock the ItemRepositoryInterface
        $this->repositoryMock = Mockery::mock(ItemRepositoryInterface::class);
        // Inject the mock repository into the ItemService
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
        // Mock the repository's getAll method to return an Eloquent Collection
        $items = new Collection([
            (object) ['id' => 1, 'name' => 'Item 1'],
            (object) ['id' => 2, 'name' => 'Item 2']
        ]);
        
        // Mock the repository method
        $this->mockRepositoryMethod('getAll', [], $items);
        
        // Call the service method
        $result = $this->itemService->getAllItems();
        
        // Assert that the returned result is a collection and has 2 items
        $this->assertCount(2, $result);
    }

    public function test_can_get_item_by_id()
    {
        // Mock the repository's findById method to return an instance of Item model
        $item = Mockery::mock(Item::class)->makePartial(); // Create a partial mock
        $item->id = 1;
        $item->name = 'Test Item'; // Set the mock attributes

        // Mock the repository's findById method to return the mocked Item model
        $this->mockRepositoryMethod('findById', [1], $item);

        // Call the service method
        $result = $this->itemService->getItemById(1);

        // Assert that the returned item's name is 'Test Item'
        $this->assertEquals('Test Item', $result->name);
    }

    public function test_can_create_item()
    {
        // Prepare the data for creating a new item
        $data = ['name' => 'New Item', 'price' => 100, 'stock' => 5];

        // Create a mock of the Item model
        $newItem = Mockery::mock(Item::class)->makePartial(); // Create a partial mock
        $newItem->name = 'New Item'; // Set the mock attributes

        // Mock the repository's create method to return the mocked Item model
        $this->mockRepositoryMethod('create', [$data], $newItem);

        // Call the service method
        $result = $this->itemService->createItem($data);

        // Assert that the returned item name is 'New Item'
        $this->assertEquals('New Item', $result->name);
    }

    public function test_can_update_item()
    {
        // Prepare the data for updating
        $data = ['name' => 'Updated Name'];

        // Create a mock of the Item model
        $updatedItem = Mockery::mock(Item::class)->makePartial(); // Create a partial mock

        // Mock the update method to return the item itself (simulating an update)
        $updatedItem->shouldReceive('update')->with($data)->andReturn(true); // Mock the update method
        $updatedItem->shouldReceive('setAttribute')
            ->with('name', 'Updated Name')
            ->once()
            ->andReturnUsing(function ($key, $value) use ($updatedItem) {
                $updatedItem->$key = $value; // Set the value on the mock object
            });

        $updatedItem->name = 'Updated Name'; // Set the updated name

        // Mock the setAttribute method to prevent the BadMethodCallException

        // Mock the repository's update method to return the mocked Item model
        $this->mockRepositoryMethod('update', [1, $data], $updatedItem);

        // Call the service method
        $result = $this->itemService->updateItem(1, $data);

        // Assert that the returned item name is 'Updated Name'
        $this->assertEquals('Updated Name', $result->name);
    }

    public function test_can_delete_item()
    {
        // Mock the repository's delete method to return true (indicating successful deletion)
        $this->mockRepositoryMethod('delete', [1], true);
    
        // Call the service method
        $result = $this->itemService->deleteItem(1);
    
        $this->repositoryMock->shouldHaveReceived('delete')->once()->with(1);
    }
}
