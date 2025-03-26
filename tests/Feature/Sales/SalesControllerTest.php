<?php

namespace Tests\Feature\Controllers;

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
}
