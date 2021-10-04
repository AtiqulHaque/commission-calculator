<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

interface FormatterContract
{
    /**
     * @param array $rawData
     * @return CsvInputData
     */
    public function format(array $rawData): CsvInputData;
}
