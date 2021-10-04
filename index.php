<?php
declare(strict_types=1);

use Annual\CommissionTask\CalculateManager;
use Annual\CommissionTask\CommissionRules\DepositRule;
use Annual\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Annual\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Annual\CommissionTask\Service\ExchangeRateService\ExchangeRateFormatter;
use Annual\CommissionTask\Service\ExchangeRateService\ExchangeRateService;
use Annual\CommissionTask\Service\Memorization\WeeklyMemorization;
use Annual\CommissionTask\Service\DataReaderService\CsvDataReader;
use Annual\CommissionTask\Service\DataReaderService\CsvFormatter;
use Annual\CommissionTask\Transactions\TransactionCollection;

require_once "./vendor/autoload.php";
$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();


$rawData = (new CsvDataReader($_ENV["CSV_URL"]))
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
