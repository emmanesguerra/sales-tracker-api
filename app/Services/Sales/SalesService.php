<?php

namespace App\Services\Sales;

use App\Services\CSV\CsvParserService;
use App\Repositories\Sales\SalesRepositoryInterface;
use App\Repositories\Item\ItemRepositoryInterface;
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
}
