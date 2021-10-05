<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\ExchangeRateService;

interface ExchangeRateContract
{
    /**
     * @param $currency
     * @param bool $cache
     */
    public function getRate($currency, $cache = true): float;

    /**
     * @return mixed
     */
    public function setFormatter(ExchangeRateFormatterContract $driver);
}
