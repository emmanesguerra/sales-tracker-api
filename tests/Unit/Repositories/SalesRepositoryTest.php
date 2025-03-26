<?php

namespace Tests\Unit\Repositories\Sales;

use App\Repositories\Sales\SalesRepository;
use App\Models\SalesOrder;
use PHPUnit\Framework\TestCase;
use Mockery;

class SalesRepositoryTest extends TestCase
{
    protected $salesRepository;

    public function setUp(): void
    {
        parent::setUp();

        // Create an instance of the repository
        $this->salesRepository = new SalesRepository();
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

        // Mock the SalesOrder model and mock the create method
        $salesOrderMock = Mockery::mock('overload:' . SalesOrder::class);
        
        // Make the create method return an instance with the given data
        $salesOrderMock->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturnUsing(function($args) {
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

        // Assert that the returned object is the mocked SalesOrder instance
        $this->assertInstanceOf(SalesOrder::class, $salesOrder);

        // Assert that the returned data matches the input
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
