<?php
declare(strict_types=1);

namespace Annual\CommissionTask\Service\ExchangeRateService;

use stdClass;

interface ExchangeRateFormatterContract
{
    /**
     * @param string $rates
     * @param $currency
     * @return stdClass
     */
    public function format($rates, $currency);
}
