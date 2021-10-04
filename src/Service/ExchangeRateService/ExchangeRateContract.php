<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\ExchangeRateService;

interface ExchangeRateContract
{
    /**
     * @param $currency
     * @param bool $cache
     * @return float
     */
    public function getRate($currency, $cache = true): float;

    /**
     * @param ExchangeRateFormatterContract $driver
     * @return mixed
     */
    public function setFormatter(ExchangeRateFormatterContract $driver);
}
