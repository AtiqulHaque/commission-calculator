<?php

use Paysera\CommissionTask\CalculateManager;
use Paysera\CommissionTask\CommissionRules\DepositRules;
use Paysera\CommissionTask\CommissionRules\WithdrawBusinessRules;
use Paysera\CommissionTask\CommissionRules\WithdrawPrivateRules;
use Paysera\CommissionTask\Service\WeeklyMemorization;
use Paysera\CommissionTask\Transactions\CsvDataReader;
use Paysera\CommissionTask\Transactions\CsvFormatter;
use Paysera\CommissionTask\Transactions\TransactionCollection;

require_once "./vendor/autoload.php";


$rawData = (new CsvDataReader("./input.csv"))
    ->setFormatter(new CsvFormatter())
    ->parseData()
    ->getData();

$collection = new TransactionCollection($rawData);

(new CalculateManager())
    ->addTransactions($collection)
    ->addRules(new DepositRules())
    ->addRules(new WithdrawBusinessRules())
    ->addRules(new WithdrawPrivateRules(new WeeklyMemorization()))
    ->applyAllRules()
    ->printCommission();
