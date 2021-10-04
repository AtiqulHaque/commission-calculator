<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\ExchangeRateService;

interface ExchangeRateFormatterContract
{
    /**
     * @param array $rates
     * @param string $currency
     * @return float
     */
    public function format(array $rates, string $currency): float;
}
