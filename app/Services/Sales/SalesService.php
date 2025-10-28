<?php

namespace App\Services\Sales;

use App\Services\CSV\CsvParserService;
use App\Repositories\Sales\SalesRepositoryInterface;
use App\Repositories\Item\ItemRepositoryInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse; 
use Exception;

class SalesService
{
    protected SalesRepositoryInterface $salesRepository;
    protected ItemRepositoryInterface $itemRepository;
    protected CsvParserService $csvParserService;

    public function __construct(
        SalesRepositoryInterface $salesRepository,
        ItemRepositoryInterface $itemRepository,
        CsvParserService $csvParserService
    ) {
        $this->salesRepository = $salesRepository;
        $this->itemRepository = $itemRepository;
        $this->csvParserService = $csvParserService;
    }
    
    public function getAllSales()
    {
        return $this->salesRepository->getAll();
    }
    
    public function getSalesByDate(string $date)
    {
        return $this->salesRepository->findByOrderDate($date);
    }

    public function uploadCsv($file): JsonResponse
    {
        $path = $file->store('csv_uploads');
        $this->processCsv(Storage::path($path));

        return response()->json([
            'message' => 'CSV file uploaded successfully',
        ]);
    }

    private function processCsv($filePath)
    {
        // Wrap the entire operation in a transaction
        DB::transaction(function () use ($filePath) {
            $records = $this->csvParserService->process($filePath);

            foreach ($records as $record) {
                $item = $this->itemRepository->findOneRecordBy('code', $record['item_code']);

                if ($item) {
                    $salesData = [
                        'order_date'  => $record['order_date'],
                        'order_time'  => $record['order_time'],
                        'item_id'     => $item->id,
                        'item_price'  => $item->price,
                        'quantity'    => $record['quantity'],
                        'total_amount'=> $record['quantity'] * $item->price,
                    ];

                    // Save the sales order using the SalesRepository
                    $this->salesRepository->create($salesData);
                } else {
                    throw new Exception("Item with code {$record['item_code']} not found.");
                }
            }
        });
    }

    public function generateReport($request): string
    {
        // Determine whether to fetch all sales or use the selected date range
        $selectAll = $request->items['selectAll'];
        $selectedDates = $request->items['selectedDates'];

        // Get sales data
        $salesData = $selectAll ? $this->getAllSales() : $this->getSalesData($selectedDates);

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header row for the Excel sheet
        $this->setHeaders($sheet);

        // Populate the data from the sales data
        $this->populateSalesData($salesData, $sheet);

        // Save the file to the storage
        $filePath = 'sales_report.xlsx';
        (new Xlsx($spreadsheet))->save(storage_path('app/' . $filePath));

        // Return the file path so it can be downloaded
        return storage_path('app/' . $filePath);
    }

    private function setHeaders($sheet)
    {
        $sheet->setCellValue('A1', 'Order Date');
        $sheet->setCellValue('B1', 'Order Time');
        $sheet->setCellValue('C1', 'Item Name');
        $sheet->setCellValue('D1', 'Item Price');
        $sheet->setCellValue('E1', 'Quantity');
        $sheet->setCellValue('F1', 'Total Amount');
    }

    private function populateSalesData($salesData, $sheet)
    {
        $row = 2; // Starting from row 2
        foreach ($salesData as $sale) {
            $time = new \DateTime($sale->order_time);
            $sheet->setCellValue('A' . $row, $sale->order_date);
            $sheet->setCellValue('B' . $row, $time->format('H:i'));
            $sheet->setCellValue('C' . $row, $sale->item->name);
            $sheet->setCellValue('D' . $row, $sale->item_price);
            $sheet->setCellValue('E' . $row, $sale->quantity);
            $sheet->setCellValue('F' . $row, $sale->total_amount);
            $row++;
        }
    }

    private function getSalesData(array $selectedDates)
    {
        // Ensure that the dates are formatted correctly, if needed
        if (count($selectedDates) != 2) {
            throw new Exception("Invalid date range provided.");
        }

        return $this->salesRepository->getSalesByDateRange($selectedDates[0], $selectedDates[1]);
    }
}
