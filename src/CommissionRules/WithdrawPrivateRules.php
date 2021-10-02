<?php
declare(strict_types=1);

namespace Paysera\CommissionTask\CommissionRules;

use Paysera\CommissionTask\Service\ExchangeRateService\ExchangeRateContract;
use Paysera\CommissionTask\Service\WeeklyMemorization;
use Paysera\CommissionTask\Transactions\Transaction;

class WithdrawPrivateRules implements RuleContract
{

    private $memorization;
    private $exchangeRateService;

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
                    if ($transaction->getAmount() > 1000) {
                        $amount = abs($transaction->getAmount() - 1000);
                        $calculatedValue = (0.3 / 100) * $amount;
                        $transaction->setCommission($calculatedValue);
                        $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
                    } else {
                        $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
                    }
                } else {
                    $rate = $this->exchangeRateService->getRate($transaction->getCurrency());
                    $amount = $transaction->getAmount();

                    if ($rate > 0) {
                        $amount = $amount / $rate;
                        if ($amount > 1000) {
                            $amount = abs($amount - 1000);
                            $calculatedValue = (0.3 / 100) * $amount;
                            $transaction->setCommission($calculatedValue);
                            $this->updateMemoryWithIndex($index, $weeklyHistory, $amount);
                        } else {
                            $this->updateMemoryWithIndex($index, $weeklyHistory, $amount);
                        }
                    }
                }

            }

            if (!empty($weeklyHistory)) {
                if ($weeklyHistory['weeklyCount'] >= 3) {
                    $amount = $transaction->getAmount();
                    $calculatedValue = (0.3 / 100) * $amount;
                    $transaction->setCommission($calculatedValue);
                    $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
                } elseif ($weeklyHistory['weeklyCount'] < 3) {
                    if ($transaction->isCurrencyEuro()) {
                        $amount = $weeklyHistory['weeklyTotal'] + $transaction->getAmount();
                        if ($amount > 1000) {
                            $amount = $transaction->getAmount();
                            $calculatedValue = (0.3 / 100) * $amount;
                            $transaction->setCommission($calculatedValue);
                            $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
                        } else {
                            $this->updateMemoryWithIndex($index, $weeklyHistory, $transaction->getAmount());
                        }
                    } else {
                        $rate = $this->exchangeRateService->getRate($transaction->getCurrency());
                        $amount = $transaction->getAmount();
                        if ($rate > 0) {
                            $amountConverted = $amount / $rate;
                            $amount = $weeklyHistory['weeklyTotal'] + $amountConverted;
                            if ($amount > 1000) {
                                $calculatedValue = (0.3 / 100) * $amountConverted;
                                $transaction->setCommission($calculatedValue);
                                $this->updateMemoryWithIndex($index, $weeklyHistory, $amountConverted);
                            } else {
                                $amount = abs(($weeklyHistory['weeklyTotal'] + $amountConverted) - 1000);
                                $calculatedValue = (0.3 / 100) * $amount;
                                $transaction->setCommission($calculatedValue);
                                $this->updateMemoryWithIndex($index, $weeklyHistory, $amount);
                            }
                        }
                    }
                }
            }
        }

        return $transaction;
    }


    public function updateMemoryWithIndex(string $index, array $weeklyHistory, float $amount)
    {
        if (!empty($weeklyHistory)) {
            $weeklyHistory['weeklyTotal'] += $amount;
            $weeklyHistory['weeklyCount'] += 1;
        } else {
            $weeklyHistory['weeklyTotal'] = $amount;
            $weeklyHistory['weeklyCount'] = 1;
        }
        $this->memorization->saveData($index, $weeklyHistory);
    }
}
