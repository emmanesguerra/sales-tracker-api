<?php 

namespace Tests\Unit\Repositories;

use App\Models\Item;
use App\Repositories\Item\ItemRepository;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;
use Mockery;

class ItemRepositoryTest extends TestCase
{
    protected $repository;
    protected $itemMock;

    public function setUp(): void
    {
        parent::setUp();

        // Create a mock for the Item model
        $this->itemMock = Mockery::mock(Item::class);

        // Instantiate the repository, passing the mock
        $this->repository = new ItemRepository($this->itemMock);
    }

    public function test_get_all_items()
    {
        // Create a mock collection of Eloquent models (Items)
        $mockItems = Mockery::mock(Collection::class);
        
        // Mock the 'all' method on the Item model to return the collection
        $this->itemMock->shouldReceive('all')
            ->once() // Ensure it's called exactly once
            ->andReturn($mockItems);

        // Call the repository method
        $result = $this->repository->getAll();

        // Assert that the result is the mock collection we defined
        $this->assertEquals($mockItems, $result);
    }

    public function test_create_item()
    {
        // Define the data to be passed to the create method
        $data = [
            'name' => 'New Item',
            'price' => 100,
            'stock' => 50
        ];

        // Mock the 'create' method on the Item model to return the mock itself (or a new instance)
        $this->itemMock->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturnSelf();  // This will return the mock instance, simulating a successful creation
    
        // Instantiate the repository with the mocked Item model
        $this->repository = new ItemRepository($this->itemMock);
    
        // Call the repository's create method
        $result = $this->repository->create($data);
    
        // Assert that the returned result is the mock Item instance
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_find_by_id()
    {
        $itemId = 1;
        
        // Mock the find method to return the mock item
        $this->itemMock->shouldReceive('find')
            ->once()
            ->with($itemId)
            ->andReturn($this->itemMock);
        
        $result = $this->repository->findById($itemId);
        
        $this->assertEquals($this->itemMock, $result);
    }

    public function test_soft_delete_item()
    {
        // Mock the 'findOrFail' method to return the mock item
        $this->itemMock->shouldReceive('findOrFail')
            ->once()
            ->andReturn($this->itemMock);

        // Mock the 'getAttribute' method to handle the 'deleted_at' field
        $this->itemMock->shouldReceive('getAttribute')
            ->with('deleted_at')
            ->andReturn(now()); // Simulating a soft delete by returning a non-null value

        // Mock the delete method to return true when called
        $this->itemMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        // Call the repository's delete method
        $this->repository->delete(1); // Using an ID as an example

        // Assert that the delete method was called once
        $this->itemMock->shouldHaveReceived('delete')->once();

        // Check if the 'deleted_at' field is set (simulating soft delete)
        $this->assertNotNull($this->itemMock->getAttribute('deleted_at'), 'Item was not soft deleted');
    }

    // Clean up Mockery after each test
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
