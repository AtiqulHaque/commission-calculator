<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

use stdClass;

interface FormatterContract
{
    /**
     * @param string $rawData
     *
     * @return stdClass
     */
    public function format($rawData);
}
