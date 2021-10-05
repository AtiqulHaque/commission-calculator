<?php

declare(strict_types=1);

namespace Annual\CommissionTask\CommissionRules;

use Annual\CommissionTask\Service\ExchangeRateService\ExchangeRateContract;
use Annual\CommissionTask\Service\Memorization\WeeklyMemorization;
use Annual\CommissionTask\Transactions\Transaction;

class WithdrawPrivateRule implements RuleContract
{
    private $memorization;
    private $exchangeRateService;
    private $weeklyCountConstrain = 3;
    private $weeklyChargeFreeAmount = 1000;
    private $commissionFee = 0.3;

    public function __construct(WeeklyMemorization $memorization, ExchangeRateContract $exchangeRateService)
    {
        $this->memorization = $memorization;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function applyRule(Transaction $transaction): Transaction
    {
        if ($transaction->isWithdraw() && $transaction->isPrivateWithdraw()) {
            $index = $transaction->getMemoryIndex();
            $weeklyHistory = $this->memorization->getData($index);
            if (empty($weeklyHistory)) {
                if ($transaction->isCurrencyEuro()) {
                    $this->processEuroWithEmptyHistory($transaction, $index, $weeklyHistory);
                } else {
                    $this->processIfNotEuroWithEmptyHistory($transaction, $index, $weeklyHistory);
                }
            }
            if (!empty($weeklyHistory)) {
                if ($weeklyHistory['weeklyCount'] >= $this->weeklyCountConstrain) {
                    $amount = $transaction->getAmount();
                    $this->updateCommission($transaction, $amount, $index, $weeklyHistory);
                } elseif ($weeklyHistory['weeklyCount'] < $this->weeklyCountConstrain) {
                    if ($transaction->isCurrencyEuro()) {
                        $this->processIfCurrencyEuro($transaction, $weeklyHistory, $index);
                    } else {
                        $this->processIfNotEuro($transaction, $weeklyHistory, $index);
                    }
                }
            }
        }

        return $transaction;
    }

    protected function updateMemoryWithIndex(string $index, array $weeklyHistory, float $amount): bool
    {
        if (!empty($weeklyHistory)) {
            $weeklyHistory['weeklyTotal'] += $amount;
            ++$weeklyHistory['weeklyCount'];
        } else {
            $weeklyHistory['weeklyTotal'] = $amount;
            $weeklyHistory['weeklyCount'] = 1;
        }

        return $this->memorization->saveData($index, $weeklyHistory);
    }

    /**
     * @param $amount
     * @return float
     */
    protected function updateCommission(
        Transaction $transaction,
        $amount,
        string $index,
        array $weeklyHistory,
        float $rate = 1.00
    ): float {
        $calculatedValue = ($this->commissionFee / 100) * $amount;

        if (!$transaction->isCurrencyEuro()) {
            $transaction->setCommission($calculatedValue * $rate);
        } else {
            $transaction->setCommission($calculatedValue);
        }

        $this->updateMemoryWithIndex($index, $weeklyHistory, $amount);

        return (float) $calculatedValue;
    }

    /**
     * @param Transaction $transaction
     * @param array $weeklyHistory
     * @param string $index
     * @return float
     */
    protected function processIfCurrencyEuro(Transaction $transaction, array $weeklyHistory, string $index): float
    {
        $amount = $weeklyHistory['weeklyTotal'];
        if ($amount > $this->weeklyChargeFreeAmount) {
            $amount = $transaction->getAmount();
            $calculatedValue = ($this->commissionFee / 100) * $amount;
            $transaction->setCommission($calculatedValue);
            $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
        } else {
            $amount = abs(($weeklyHistory['weeklyTotal'] + $transaction->getAmount()) - $this->weeklyChargeFreeAmount);
            $calculatedValue = ($this->commissionFee / 100) * $amount;
            $transaction->setCommission($calculatedValue);
            $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
        }

        return $calculatedValue;
    }

    /**
     * @param Transaction $transaction
     * @param array $weeklyHistory
     * @param string $index
     * @return float
     */
    protected function processIfNotEuro(Transaction $transaction, array $weeklyHistory, string $index): float
    {
        $rate = $this->exchangeRateService->getRate($transaction->getCurrency());
        $amount = $transaction->getAmount();
        if ($rate > 0) {
            $amountConverted = $amount / $rate;
            $amount = $weeklyHistory['weeklyTotal'];
            if ($amount > $this->weeklyChargeFreeAmount) {
                $this->updateCommission($transaction, $amountConverted, $index, $weeklyHistory, $rate);
            } else {
                $amount = abs(($weeklyHistory['weeklyTotal'] + $amountConverted) - $this->weeklyChargeFreeAmount);
                $this->updateCommission($transaction, $amount, $index, $weeklyHistory, $rate);
            }
        }

        return $amount;
    }

    /**
     * @param Transaction $transaction
     * @param string $index
     * @param array $weeklyHistory
     * @return float
     */
    protected function processIfNotEuroWithEmptyHistory(
        Transaction $transaction,
        string $index,
        array $weeklyHistory
    ): float {
        $rate = $this->exchangeRateService->getRate($transaction->getCurrency());
        $amount = $transaction->getAmount();

        if ($rate > 0) {
            $amount = $amount / $rate;
            if ($amount > $this->weeklyChargeFreeAmount) {
                $amount = abs($amount - $this->weeklyChargeFreeAmount);
                $this->updateCommission($transaction, $amount, $index, $weeklyHistory, $rate);
            } else {
                $this->updateMemoryWithIndex($index, $weeklyHistory, $amount);
            }
        }

        return (float) $amount;
    }

    /**
     * @param Transaction $transaction
     * @param string $index
     * @param array $weeklyHistory
     * @return float
     */
    protected function processEuroWithEmptyHistory(Transaction $transaction, string $index, array $weeklyHistory): float
    {
        if ($transaction->getAmount() > $this->weeklyChargeFreeAmount) {
            $amount = abs($transaction->getAmount() - $this->weeklyChargeFreeAmount);
            $calculatedValue = ($this->commissionFee / 100) * $amount;
            $transaction->setCommission($calculatedValue);
            $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
        } else {
            $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
        }

        return (float) $transaction->getAmount();
    }

    /**
     * @param int $weeklyCountConstrain
     */
    protected function setWeeklyCountConstrain(int $weeklyCountConstrain)
    {
        $this->weeklyCountConstrain = $weeklyCountConstrain;
    }

    /**
     * @param int $weeklyChargeFreeAmount
     */
    protected function setWeeklyChargeFreeAmount(int $weeklyChargeFreeAmount)
    {
        $this->weeklyChargeFreeAmount = $weeklyChargeFreeAmount;
    }

    /**
     * @param float $commissionFee
     */
    protected function setCommissionFee(float $commissionFee)
    {
        $this->commissionFee = $commissionFee;
    }
}
