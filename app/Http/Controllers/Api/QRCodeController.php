<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QR\QRCodeService;
use Illuminate\Http\Request;

class QRCodeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function generate(Request $request)
    {
        // Use the service to handle the QR code generation and download
        return $this->qrCodeService->generateQRCodeForPdf($request->items);
    }
}
