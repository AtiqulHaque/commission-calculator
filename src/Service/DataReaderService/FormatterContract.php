<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\DataReaderService;

interface FormatterContract
{
    public function format(array $rawData): CsvInputData;
}
