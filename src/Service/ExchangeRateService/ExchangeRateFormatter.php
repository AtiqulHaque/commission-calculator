<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\ExchangeRateService;

class ExchangeRateFormatter implements ExchangeRateFormatterContract
{
    public function format(array $rates, string $currency): float
    {
        if (!empty($rates['rates']) && !empty($rates['rates'][$currency])) {
            return floatval($rates['rates'][$currency]);
        }

        return 1.00;
    }
}
