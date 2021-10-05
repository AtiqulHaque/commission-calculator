<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\DataReaderService;

use Generator;

interface DataReaderContract
{
    public function setFormatter(FormatterContract $formatter): CsvDataReader;

    public function getDataFromFile(): Generator;
}
