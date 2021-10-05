<?php

declare(strict_types=1);

namespace Annual\CommissionTask;

use Annual\CommissionTask\CommissionRules\RuleContract;
use Annual\CommissionTask\Transactions\Transaction;

class CalculateManager
{
    public $rules = [];

    /**
     * @return $this
     */
    public function addRule(RuleContract $rule)
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Transaction $transaction
     */
    public function applyAllRulesUsingGenerator($transaction)
    {
        if (!empty($this->rules)) {
            foreach ($this->rules as $eachRules) {
                if ($eachRules instanceof RuleContract) {
                    $eachRules->applyRule($transaction);
                }
            }
        }

        return $transaction;
    }
}
