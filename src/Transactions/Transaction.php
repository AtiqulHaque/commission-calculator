<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Transactions;

use Carbon\Carbon;
use stdClass;

class Transaction
{
    private $userIdentification;
    private $userType;
    private $operationType;
    private $operationAmount;
    private $operationCurrency;
    private $transactionDate;
    private $commission = 0.00;

    public function __construct(stdClass $obj)
    {
        $this->transactionDate = $obj->transactionDate;
        $this->userIdentification = $obj->userIdentification;
        $this->userType = $obj->userType;
        $this->operationType = $obj->operationType;
        $this->operationAmount = $obj->operationAmount;
        $this->operationCurrency = $obj->operationCurrency;
    }

    public function isCurrencyEuro(): bool
    {
        return $this->operationCurrency === 'EUR';
    }

    public function getAmount(): float
    {
        return $this->operationAmount;
    }

    public function getCurrency(): string
    {
        return $this->operationCurrency;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @return string
     */
    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * @return string
     */
    public function getOperationCurrency(): string
    {
        return $this->operationCurrency;
    }

    /**
     * @return Carbon
     */

    public function getTransactionDate(): Carbon
    {
        try {
            return new Carbon($this->transactionDate);
        } catch (\Exception $e) {
        }
    }

    /**
     * @return float
     */
    public function getCommission(): float
    {
        return $this->commission;
    }


    /**
     * @param float $commission
     */
    public function setCommission(float $commission)
    {
        $this->commission = $commission;
    }

    public function isDeposit(): bool
    {
        return ($this->operationType == "deposit");
    }

    public function isWithdraw(): bool
    {
        return ($this->operationType == "withdraw");
    }


    public function isPrivateWithdraw(): bool
    {
        return ($this->userType == "private");
    }


    public function isBusinessWithdraw(): bool
    {
        return ($this->userType == "business");
    }


}
