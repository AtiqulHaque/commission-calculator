<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Transactions;

use stdClass;

class CsvFormatter implements FormatterContract
{
    /**
     * @param string $rawData
     * @return stdClass
     */
    public function format($rawData): stdClass
    {
        $stdObject = new stdClass();
        $stdObject->transactionDate = !empty($rawData[0]) ? $rawData[0] : null;
        $stdObject->userIdentification = !empty($rawData[1]) ? $rawData[1] : null;
        $stdObject->userType = !empty($rawData[2]) ? $rawData[2] : null;
        $stdObject->operationType = !empty($rawData[3]) ? $rawData[3] : null;
        $stdObject->operationAmount = !empty($rawData[4]) ? floatval($rawData[4]) : null;
        $stdObject->operationCurrency = !empty($rawData[5]) ? $rawData[5] : null;

        return $stdObject;
    }
}
