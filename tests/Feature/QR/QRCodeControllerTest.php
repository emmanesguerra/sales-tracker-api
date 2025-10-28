<?php

namespace Tests\Unit\Http\Controllers\Api;

use App\Http\Controllers\Api\QRCodeController;
use App\Services\QR\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Mockery;
use Tests\TestCase;

class QRCodeControllerTest extends TestCase
{
    protected $qrCodeServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mock QRCodeService
        $this->qrCodeServiceMock = Mockery::mock(QRCodeService::class);
    }

    public function testGenerateQRCodeForPdf()
    {
        // Step 1: Prepare input data
        $items = [1, 2, 3]; // Example items for QR code generation

        // Step 2: Mock the QRCodeService to return a fake response (simulating the service behavior)
        $this->qrCodeServiceMock
            ->shouldReceive('generateQRCodeForPdf')
            ->with($items)
            ->once()
            ->andReturn(Response::make('Mocked PDF response', 200, ['Content-Type' => 'application/pdf']));

        // Step 3: Bind the mocked service to the container so it gets injected into the controller
        $this->app->instance(QRCodeService::class, $this->qrCodeServiceMock);

        // Step 4: Create a request with the items
        $request = Request::create('/api/generate-qr-code', 'GET', ['items' => $items]);

        // Step 5: Instantiate the controller and call the method
        $controller = new QRCodeController($this->qrCodeServiceMock);
        $response = $controller->generate($request);

        // Step 6: Assert the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
