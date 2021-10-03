<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

interface DataReaderContract
{
    public function setFormatter(FormatterContract $formatter): CsvDataReader;

    public function parseData(): CsvDataReader;

    public function getData(): array;
}
