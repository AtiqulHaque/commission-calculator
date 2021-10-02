<?php
declare(strict_types=1);

namespace Paysera\CommissionTask\Service\ExchangeRateService;

interface ExchangeRateContract
{
    public function getRate($currency, $cache = true);

    public function setFormatter(ExchangeRateFormatterContract $driver);


}
