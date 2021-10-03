<?php
declare(strict_types=1);

namespace Annual\CommissionTask\CommissionRules;

use Annual\CommissionTask\Transactions\Transaction;

class WithdrawBusinessRule implements RuleContract
{

    public function applyRule(Transaction $transaction): Transaction
    {
        if ($transaction->isWithdraw() && $transaction->isBusinessWithdraw()) {
            $calculatedValue = (0.5 / 100) * $transaction->getAmount();
            $transaction->setCommission($calculatedValue);
        }
        return $transaction;
    }
}
