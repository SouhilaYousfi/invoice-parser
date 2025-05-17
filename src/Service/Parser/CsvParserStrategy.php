<?php

declare(strict_types=1);

namespace App\Service\Parser;

class CsvParserStrategy implements IParserStrategy
{
    public function parse(string $filePath): array
    {
        $rows = array_map(function($row) {
            return str_getcsv($row, "\t");
        }, file($filePath));

        $invoices = [];
        foreach ($rows as $row) {
            if (count($row) >= 3) {
                $invoices[] = [
                    'amount' => $row[0],
                    'name' => $row[2]
                ];
            }
        }
        return $invoices;
    }
}