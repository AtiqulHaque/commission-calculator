<?php

declare(strict_types=1);

namespace Annual\CommissionTask;

use Annual\CommissionTask\CommissionRules\RuleContract;
use Annual\CommissionTask\Transactions\Transaction;
use Annual\CommissionTask\Transactions\TransactionCollection;

class CalculateManager
{
    /** @var TransactionCollection $transactions */
    public $transactions = null;
    public $rules = [];

    /**
     * @param TransactionCollection $collection
     * @return $this
     */
    public function addTransactions(TransactionCollection $collection)
    {
        $this->transactions = $collection;

        return $this;
    }

    /**
     * @param RuleContract $rule
     * @return $this
     */
    public function addRule(RuleContract $rule)
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @return $this
     */
    public function applyAllRules()
    {
        $this->transactions->each(function ($eachTransaction) {
            if (!empty($this->rules)) {
                foreach ($this->rules as $eachRules) {
                    if ($eachRules instanceof RuleContract) {
                        $eachRules->applyRule($eachTransaction);
                    }
                }
            }
        });

        return $this;
    }

    /**
     *
     */
    public function printCommission()
    {
        $this->transactions->each(function ($eachTransaction) {
            /** @var Transaction $eachTransaction */
            echo number_format($eachTransaction->getCommission(), 2, '.', '') . "\n";
        });
    }

    /**
     * @return array
     */
    public function getAllTransactions(): array
    {
        return $this->transactions->all();
    }
}
