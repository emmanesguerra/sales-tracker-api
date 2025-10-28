<?php
namespace Tests\Unit\Repositories\Sales;

use App\Repositories\Sales\SalesRepository;
use App\Models\SalesOrder;
use PHPUnit\Framework\TestCase;
use Mockery;

class SalesRepositoryTest extends TestCase
{
    protected $salesRepository;
    protected $salesOrderMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mock the SalesOrder model if needed for repository
        $this->salesOrderMock = Mockery::mock(SalesOrder::class);

        // Create an instance of the repository, passing the mocked SalesOrder model if needed
        $this->salesRepository = new SalesRepository($this->salesOrderMock);
    }

    public function testCreate()
    {
        // Define the data to be passed to the create method
        $data = [
            'order_date'  => '2025-03-25',
            'order_time'  => '12:00:00',
            'item_id'     => 1,
            'item_price'  => 100,
            'quantity'    => 5,
            'total_amount'=> 500,
        ];

        // Instead of mocking the SalesOrder create method, mock the repository create method
        $this->salesOrderMock->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturnUsing(function($args) {
                // Simulate creating a SalesOrder instance
                $salesOrder = new SalesOrder();
                $salesOrder->order_date = $args['order_date'];
                $salesOrder->order_time = $args['order_time'];
                $salesOrder->item_id = $args['item_id'];
                $salesOrder->item_price = $args['item_price'];
                $salesOrder->quantity = $args['quantity'];
                $salesOrder->total_amount = $args['total_amount'];
                return $salesOrder;
            });

        // Call the create method on the repository
        $salesOrder = $this->salesRepository->create($data);

        // Assert that the returned object is an instance of SalesOrder
        $this->assertInstanceOf(SalesOrder::class, $salesOrder);

        // Assert that the returned data matches the input data
        $this->assertEquals($data['order_date'], $salesOrder->order_date);
        $this->assertEquals($data['order_time'], $salesOrder->order_time);
        $this->assertEquals($data['item_id'], $salesOrder->item_id);
        $this->assertEquals($data['item_price'], $salesOrder->item_price);
        $this->assertEquals($data['quantity'], $salesOrder->quantity);
        $this->assertEquals($data['total_amount'], $salesOrder->total_amount);
    }

    public function tearDown(): void
    {
        // Close any Mockery expectations
        Mockery::close();

        parent::tearDown();
    }
}
