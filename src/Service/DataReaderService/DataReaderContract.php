<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\DataReaderService;

interface DataReaderContract
{
    /**
     * @param FormatterContract $formatter
     * @return CsvDataReader
     */
    public function setFormatter(FormatterContract $formatter): CsvDataReader;

    /**
     * @return CsvDataReader
     */
    public function parseData(): CsvDataReader;

    /**
     * @return array
     */
    public function getData(): array;
}
