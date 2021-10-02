<?php

declare(strict_types=1);

namespace Paysera\CommissionTask;

use Paysera\CommissionTask\CommissionRules\RuleContract;
use Paysera\CommissionTask\Transactions\Transaction;
use Paysera\CommissionTask\Transactions\TransactionCollection;

class CalculateManager
{
    /** @var TransactionCollection $transactions */
    public $transactions = null;
    public $rules = array();


    /**
     * @param TransactionCollection $collection
     * @return $this
     */
    public function addTransactions(TransactionCollection $collection)
    {
        $this->transactions = $collection;
        return $this;
    }


    public function addRules(RuleContract $rule)
    {
        $this->rules[] = $rule;
        return $this;
    }

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

    public function printCommission()
    {

        $this->transactions->each(function ($eachTransaction) {
            /** @var Transaction $eachTransaction */
            echo
                $eachTransaction->getMemoryIndex() ."  ".
                $eachTransaction->getCurrency() ."  ".
                $eachTransaction->getTransactionDate() ."  ".
                $eachTransaction->getAmount() .
                "===". $eachTransaction->getCommission() . "\n";
        });
    }
}
