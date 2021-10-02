<?php
declare(strict_types=1);

namespace Paysera\CommissionTask\Service\ExchangeRateService;

class ExchangeRateFormatter implements ExchangeRateFormatterContract
{
    /**
     * @param $rates
     * @param $currency
     * @return float
     */
    public function format($rates, $currency): float
    {
        if (!empty($rates['rates']) && !empty($rates['rates'][$currency])) {
            return floatval($rates['rates'][$currency]);
        }
        return 0;
    }
}
