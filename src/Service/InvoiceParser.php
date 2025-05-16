<?php

declare(strict_types=1);


namespace App\Service;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;


class InvoiceParser
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /*
     * Parse and update database from the input filePath
     */
    public function parse(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File not found: {$filePath}");
        }
        if(str_contains($filePath, "json")) {
           $this->parseJsonFile($filePath);
        }elseif (str_contains($filePath, "csv")) {
            $this->parseCsvFile($filePath);
        }else {
            throw new InvalidArgumentException("Extension not supported: {$filePath}");
        }
    }

    private function parseJsonFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $lines = preg_split("/\r\n|\n|\r/", $content);
        $amount="";
        $name="";
        foreach ($lines as $line) {
            if(str_contains($line, "montant")) {
                $amount = $this->extractValue($line);
            } elseif(str_contains($line, "nom")) {
                $name = $this->extractValue($line);
            } elseif(str_contains($line, "}")) {
                $this->updateInvoice($amount, $name);
            }
        }
    }


    private function parseCsvFile(string $filePath): void
    {
        $rows = array_map(function ($row) {
            return str_getcsv($row, "\t");
        }, file($filePath));
        foreach ($rows as $row) {
            if (count($row)>=3) {
                $amount = $row[0];
                $name = $row[2];
                $this->updateInvoice($amount, $name);
            }
        }
    }

    private function updateInvoice(string $amount, string $name): void{
        $sql = 'UPDATE invoice SET amount = :amount WHERE name = :name';
        $this->entityManager->getConnection()->prepare($sql)->executeStatement([
            'amount' => $amount,
            'name' => $name
        ]);
    }

    private function extractValue(string $line): string{

        $parts = explode(": ", $line);
        // ["montant", "852.38,"]
        // ["nom", "Frank Green,"]
        $value = trim($parts[1], ",");
        return $value;
    }
}
