<?php
declare(strict_types=1);

namespace Annual\CommissionTask\CommissionRules;

use Annual\CommissionTask\Transactions\Transaction;

interface RuleContract
{
    public function applyRule(Transaction $transaction): Transaction;
}
