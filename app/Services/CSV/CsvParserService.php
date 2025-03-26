<?php

namespace App\Services\CSV;

use Exception;

class CsvParserService
{
    public function process(string $filePath): array
    {
        $parsedData = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                $record = array_combine($header, $row);
                $parsedData[] = $record;
            }

            fclose($handle);
        } else {
            throw new Exception('Failed to open CSV file for reading');
        }

        return $parsedData;
    }
}
