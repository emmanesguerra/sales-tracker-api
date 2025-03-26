<?php

namespace Tests\Feature\Controllers;

use App\Models\SalesOrder;
use App\Models\Item;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Services\Sales\SalesService;
use Mockery;

class SalesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;
    protected $user;
    protected $token;

    // Set up common data for tests
    public function setUp(): void
    {
        parent::setUp();

        // Create a tenant and associate the user with the tenant
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Generate a token for the user
        $this->token = $this->user->createToken('TestApp')->plainTextToken;
    }

    // Helper method to build the URL for a given path
    protected function buildUrl($path)
    {
        return 'http://' . $this->tenant->subdomain . '.' . env('APP_DOMAIN') . $path;
    }

    public function test_upload_csv()
    {
        // Create a mock for the SalesService
        $salesServiceMock = Mockery::mock(SalesService::class);
        $this->app->instance(SalesService::class, $salesServiceMock);
        
        // Fake the storage disk
        Storage::fake('csv_uploads');
        
        // Prepare a fake file to upload
        $file = UploadedFile::fake()->create('test.csv', 100);
        
        // Expect that the file will be saved on the fake disk
        // Since the uploadCsv method returns a JsonResponse, we'll mock it to return a JsonResponse
        $salesServiceMock->shouldReceive('uploadCsv')
                        ->once()
                        ->with($file)
                        ->andReturn(response()->json([
                            'message' => 'File uploaded successfully!',
                            'path' => 'csv_uploads/test.csv'
                        ], 200));

        // Build the URL for the upload endpoint
        $url = $this->buildUrl('/api/upload-csv');
        
        // Send the request with authentication header and attach the file
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->json('POST', $url, [
            'file' => $file,
        ]);
        
        // Assert that the response is correct
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'File uploaded successfully!',
            'path' => 'csv_uploads/test.csv',
        ]);
    }

    public function test_upload_csv_no_file()
    {
        $url = $this->buildUrl('/api/upload-csv');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->json('POST', $url);

        $response->assertStatus(422); 
        $response->assertJson([
            'errors' => [
                'file' => ['No file was uploaded.']
            ]
        ]);
    }

    public function test_index_returns_sales_by_date()
    {
        // Create mock data for sales and items
        $item = Item::factory()->create(['name' => 'Test Item', 'price' => 100]);
        $salesOrder = SalesOrder::factory()->create([
            'order_date' => '2025-03-25',
            'order_time' => '14:00',
            'item_id' => $item->id,
            'quantity' => 2,
            'total_amount' => 200,
        ]);

        // Mock SalesService to return the created sales order
        $salesServiceMock = Mockery::mock(SalesService::class);
        $this->app->instance(SalesService::class, $salesServiceMock);
        
        // Expect the service to return the sales data by date
        $salesServiceMock->shouldReceive('getSalesByDate')
                         ->once()
                         ->with('2025-03-25')
                         ->andReturn(collect([$salesOrder]));

        // Build the URL for the index endpoint
        $url = $this->buildUrl('/api/sales-orders');

        // Send a GET request to the endpoint with the date parameter
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->json('GET', $url, [
            'date' => '2025-03-25',
        ]);

        // Assert that the response status is 200
        $response->assertStatus(200);
    }
}
