<?php

declare(strict_types=1);

namespace App\Service\Parser;

use InvalidArgumentException;

interface IParserStrategy{

    /**
     * Parse the given file and return an array of invoice data
     *
     * @param string $filePath Path to the file to parse
     * @return array Array of invoice data with amount and name
     * @throws InvalidArgumentException If file cannot be read or has invalid format
     */
    public function parse(string $filePath):array;
}