<?php

declare(strict_types=1);


namespace App\Service\Parser;

class JsonParserStrategy implements IParserStrategy
{
  public function parse(string $filePath): array{
      $content = file_get_contents($filePath);
      $lines = preg_split("/\r\n|\n|\r/", $content);
      $invoices=[];
      $currentInvoice = [];
      foreach ($lines as $line) {
          if (str_contains($line, 'montant')) {
              $currentInvoice['amount'] = $this->extractValue($line);
          } elseif (str_contains($line, 'nom')) {
              $currentInvoice['name'] = $this->extractValue($line);
          } elseif (str_contains($line, '}') && !empty($currentInvoice)) {
              $invoices[] = $currentInvoice;
              $currentInvoice = [];
          }
      }
      return $invoices;
  }

    private function extractValue(string $line): string
    {
        $parts = explode(': ', $line);
        $value = rtrim($parts[1], ',');
        return $value;
    }
}
