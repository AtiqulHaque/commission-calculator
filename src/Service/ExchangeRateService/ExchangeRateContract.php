<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\ExchangeRateService;

interface ExchangeRateContract
{
    public function getRate($currency, $cache = true): float;

    public function setFormatter(ExchangeRateFormatterContract $driver);
}
