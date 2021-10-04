<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

use Annual\CommissionTask\Helper;

class Transaction
{
    private $userIdentification;
    private $userType;
    private $operationType;
    private $operationAmount;
    private $operationCurrency;
    private $transactionDate;
    private $commission = 0.00;

    public function __construct(CsvInputData $obj)
    {
        $this->transactionDate = $obj->getTransactionDate();
        $this->userIdentification = $obj->getUserIdentification();
        $this->userType = $obj->getUserType();
        $this->operationType = $obj->getOperationType();
        $this->operationAmount = $obj->getOperationAmount();
        $this->operationCurrency = $obj->getOperationCurrency();
    }

    /**
     * @return bool
     */
    public function isCurrencyEuro(): bool
    {
        return $this->operationCurrency === 'EUR';
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->operationAmount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->operationCurrency;
    }

    /**
     * @return string
     */
    public function getTransactionDate(): string
    {
        return $this->transactionDate;
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

    /**
     * @return bool
     */
    public function isDeposit(): bool
    {
        return $this->operationType === 'deposit';
    }

    /**
     * @return bool
     */
    public function isWithdraw(): bool
    {
        return $this->operationType === 'withdraw';
    }

    /**
     * @return bool
     */
    public function isPrivateWithdraw(): bool
    {
        return $this->userType === 'private';
    }

    /**
     * @return bool
     */
    public function isBusinessWithdraw(): bool
    {
        return $this->userType === 'business';
    }

    /**
     * @return string
     */
    public function getMemoryIndex()
    {
        return $index = "{$this->getUserIdentification()}:" .
            Helper::calculateWeek($this->getTransactionDate());
    }

    /**
     * @return mixed
     */
    public function getUserIdentification()
    {
        return $this->userIdentification;
    }
}
