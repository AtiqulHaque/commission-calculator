<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

class CsvInputData
{
    public $transactionDate = '';
    public $userIdentification = '';
    public $userType = '';
    public $operationType = '';
    public $operationAmount = 0.00;
    public $operationCurrency = '';

    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    /**
     * @param string $transactionDate
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    public function getUserIdentification(): string
    {
        return $this->userIdentification;
    }

    /**
     * @param null $userIdentification
     */
    public function setUserIdentification($userIdentification)
    {
        $this->userIdentification = $userIdentification;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @param null $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * @param null $operationType
     */
    public function setOperationType($operationType)
    {
        $this->operationType = $operationType;
    }

    public function getOperationAmount(): float
    {
        return $this->operationAmount;
    }

    /**
     * @param null $operationAmount
     */
    public function setOperationAmount($operationAmount)
    {
        $this->operationAmount = $operationAmount;
    }

    public function getOperationCurrency(): string
    {
        return $this->operationCurrency;
    }

    /**
     * @param null $operationCurrency
     */
    public function setOperationCurrency($operationCurrency)
    {
        $this->operationCurrency = $operationCurrency;
    }
}
