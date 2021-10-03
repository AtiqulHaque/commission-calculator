<?php
declare(strict_types=1);

namespace Annual\CommissionTask\CommissionRules;

use Annual\CommissionTask\Transactions\Transaction;

class DepositRule implements RuleContract
{

    /**
     * @param Transaction $transaction
     * @return Transaction
     */
    public function applyRule(Transaction $transaction): Transaction
    {
        if ($transaction->isDeposit()) {
            $transaction->setCommission((0.03 / 100) * $transaction->getAmount());
        }
        return $transaction;
    }
}
