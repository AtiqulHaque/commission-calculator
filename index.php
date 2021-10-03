<?php

use Carbon\Carbon;
use Annual\CommissionTask\CalculateManager;
use Annual\CommissionTask\CommissionRules\DepositRule;
use Annual\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Annual\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Annual\CommissionTask\Service\ExchangeRateService\ExchangeRateFormatter;
use Annual\CommissionTask\Service\ExchangeRateService\ExchangeRateService;
use Annual\CommissionTask\Service\WeeklyMemorization;
use Annual\CommissionTask\Transactions\CsvDataReader;
use Annual\CommissionTask\Transactions\CsvFormatter;
use Annual\CommissionTask\Transactions\TransactionCollection;

require_once "./vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$rawData = (new CsvDataReader("./input.csv"))
    ->setFormatter(new CsvFormatter())
    ->parseData()
    ->getData();

$collection = new TransactionCollection($rawData);

$exchangeRateServiceObj =  (new ExchangeRateService($_ENV["EXCHANGE_RATE_URL"]))
    ->setFormatter(new ExchangeRateFormatter());

(new CalculateManager())
    ->addTransactions($collection)
    ->addRule(new DepositRule())
    ->addRule(new WithdrawBusinessRule())
    ->addRule(new WithdrawPrivateRule(new WeeklyMemorization(), $exchangeRateServiceObj))
    ->applyAllRules()
    ->printCommission();
