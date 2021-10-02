<?php

use Carbon\Carbon;
use Paysera\CommissionTask\CalculateManager;
use Paysera\CommissionTask\CommissionRules\DepositRules;
use Paysera\CommissionTask\CommissionRules\WithdrawBusinessRules;
use Paysera\CommissionTask\CommissionRules\WithdrawPrivateRules;
use Paysera\CommissionTask\Service\ExchangeRateService\ExchangeRateFormatter;
use Paysera\CommissionTask\Service\ExchangeRateService\ExchangeRateService;
use Paysera\CommissionTask\Service\WeeklyMemorization;
use Paysera\CommissionTask\Transactions\CsvDataReader;
use Paysera\CommissionTask\Transactions\CsvFormatter;
use Paysera\CommissionTask\Transactions\TransactionCollection;

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
    ->addRules(new DepositRules())
    ->addRules(new WithdrawBusinessRules())
    ->addRules(new WithdrawPrivateRules(new WeeklyMemorization(), $exchangeRateServiceObj))
    ->applyAllRules()
    ->printCommission();
