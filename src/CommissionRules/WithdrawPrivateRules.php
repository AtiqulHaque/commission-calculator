<?php
declare(strict_types=1);

namespace Paysera\CommissionTask\CommissionRules;

use Paysera\CommissionTask\Service\WeeklyMemorization;
use Paysera\CommissionTask\Transactions\Transaction;

class WithdrawPrivateRules implements RuleContract
{

    private $memorization;

    public function __construct(WeeklyMemorization $memorization)
    {
        $this->memorization = $memorization;
    }

    public function applyRule(Transaction $transaction): Transaction
    {
        // TODO: Implement applyRule() method.
        $this->memorization->getData();
        return $transaction;
    }
}
