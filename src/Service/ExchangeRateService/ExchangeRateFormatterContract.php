<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\ExchangeRateService;

interface ExchangeRateFormatterContract
{
    public function format(array $rates, string $currency): float;
}
