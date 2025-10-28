<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Sales\SalesService;
use Illuminate\Http\Request;
use App\Http\Requests\UploadCsvRequest;
use App\Http\Requests\SalesOrderRequest;
use App\Http\Resources\SalesResource;


class SalesController extends Controller
{
    protected $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    public function index(SalesOrderRequest $request)
    {   
        $date = $request->date;
    
        if ($date) {
            $list = $this->salesService->getSalesByDate($date);
        } else {
            $list = $this->salesService->getAllSales();
        }

        $data = SalesResource::collection($list)->resource;
        return response()->json($data, 200);
    }

    public function upload(UploadCsvRequest $request)
    {
        $file = $request->file('file');
        return $this->salesService->uploadCsv($file);
    }

    public function generate(Request $request)
    {
        try {
            $reportFile = $this->salesService->generateReport($request);
            return response()->download($reportFile);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }
}
