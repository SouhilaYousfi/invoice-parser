<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Parser\ParserFactory;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class InvoiceParser
{
    private EntityManagerInterface $entityManager;
    private ParserFactory $parserFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParserFactory $parserFactory
    ) {
        $this->entityManager = $entityManager;
        $this->parserFactory = $parserFactory;
    }

    /**
     * Parse the given file and update invoices in the database
     *
     * @param string $filePath Path to the file to parse
     * @throws InvalidArgumentException If file cannot be parsed
     */
    public function parse(string $filePath): void
    {
        try {
            $parser = $this->parserFactory->createParser($filePath);
            $invoices = $parser->parse($filePath);
            foreach ($invoices as $invoice) {
                if (isset($invoice['amount']) && isset($invoice['name'])) {
                    $this->updateInvoice($invoice['amount'], $invoice['name']);
                }
            }
        } catch (\Throwable $e) {
            throw new InvalidArgumentException(
                "Failed to parse file {$filePath}: ");
        }
    }

    /**
     * Update an invoice in the database
     *
     * @param string $amount The invoice amount
     * @param string $name The invoice name
     * @throws Exception
     */
    private function updateInvoice(string $amount, string $name): void
    {
        $sql = 'UPDATE invoice SET amount = :amount WHERE name = :name';
        $this->entityManager->getConnection()->prepare($sql)->executeQuery([
            'amount' => $amount,
            'name' => $name
        ]);
    }
}
