<?php

declare(strict_types=1);

namespace App\Service\Parser;

use http\Exception\InvalidArgumentException;

class ParserFactory{
    public function createParser(string $filePath): IParserStrategy{
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File does not exist {$filePath}");
        }
        if(str_contains($filePath,'json')){
            return  new JsonParserStrategy();
        }
        if(str_contains($filePath,'csv')){
            return  new CsvParserStrategy();
        }
        throw new InvalidArgumentException("Supported format are json or csv");
    }
}