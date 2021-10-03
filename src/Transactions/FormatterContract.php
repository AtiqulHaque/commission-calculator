<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

interface FormatterContract
{
    public function format(array $rawData): CsvInputData;
}
