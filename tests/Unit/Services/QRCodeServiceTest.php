<?php

namespace Tests\Unit\Services\QR;

use Tests\TestCase;
use App\Services\QR\QRCodeService;
use App\Repositories\Item\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Mockery;
use Dompdf\Dompdf;

class QRCodeServiceTest extends TestCase
{
    protected $qrCodeService;
    protected $itemRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mock the ItemRepositoryInterface
        $this->itemRepositoryMock = Mockery::mock(ItemRepositoryInterface::class);

        // Instantiate the service with the mocked repository
        $this->qrCodeService = new QRCodeService($this->itemRepositoryMock);
    }

    public function testGeneratesQRCodeHtmlCorrectly()
    {
        $items = new Collection([
            (object) ['id' => 1, 'name' => 'Item 1'],
            (object) ['id' => 2, 'name' => 'Item 2'],
        ]);

        $htmlOutput = $this->invokeMethod($this->qrCodeService, 'generateQrCodesHtml', [$items, true]);

        $this->assertStringContainsString('Item 1', $htmlOutput);
        $this->assertStringContainsString('Item 2', $htmlOutput);
        $this->assertStringContainsString('data:image/png;base64,', $htmlOutput);
    }

    public function testCreatesAPdfFile()
    {
        // Mock the File facade to avoid actual file operations
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('makeDirectory')->andReturn(true);
        File::shouldReceive('put')->once()->andReturn(true);
    
        $htmlQrList = "<div>QR Code</div>";
        $pdfFilePath = $this->invokeMethod($this->qrCodeService, 'createPdf', [$htmlQrList, true]);
    
        // Ensure the file path is returned (even though we are mocking file creation)
        $this->assertNotEmpty($pdfFilePath);
    }

    public function testHandlesPdfGenerationAndDownload()
    {
        // Step 1: Fake the storage to avoid writing actual files
        Storage::fake('local');
    
        // Step 2: Mock the repository response
        $items = new Collection([
            (object) ['id' => 1, 'name' => 'Item 1'],
        ]);
    
        // Mocking the ItemRepositoryInterface
        $this->itemRepositoryMock
            ->shouldReceive('findByArray')
            ->once()
            ->andReturn($items);
    
        // Step 3: Mock the PDF generation and response
        $responseMock = \Mockery::mock('Illuminate\Http\Response');
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('headers->get')->with('content-type')->andReturn('application/pdf');
        
        // Mock the response call to return a mocked response.
        $this->app->instance('Illuminate\Http\Response', $responseMock);
    
        // Step 4: Prepare the form data
        $formData = [
            'items' => [
                'selectedItems' => [1],
                'isGrid' => true,
            ],
        ];
    
        // Step 5: Call the actual service method
        $response = $this->qrCodeService->generateQRCodeForPdf($formData['items']);
    
        // Step 6: Assert response was returned and headers are correct
        $this->assertNotNull($response, 'Expected a response but got null');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Helper function to invoke private/protected methods.
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
