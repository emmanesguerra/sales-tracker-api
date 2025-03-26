<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Sales\SalesService;
use Illuminate\Http\Request;
use App\Http\Requests\UploadCsvRequest;

class SalesController extends Controller
{
    protected $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    public function upload(UploadCsvRequest $request)
    {
        $file = $request->file('file');
        return $this->salesService->uploadCsv($file);
    }
}
