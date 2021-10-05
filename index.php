<?php
declare(strict_types=1);

use Annual\CommissionTask\CalculateManager;
use Annual\CommissionTask\CommissionRules\DepositRule;
use Annual\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Annual\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Annual\CommissionTask\Service\DataReaderService\CsvDataReader;
use Annual\CommissionTask\Service\DataReaderService\CsvFormatter;
use Annual\CommissionTask\Service\ExchangeRateService\ExchangeRateFormatter;
use Annual\CommissionTask\Service\ExchangeRateService\ExchangeRateService;
use Annual\CommissionTask\Service\Memorization\WeeklyMemorization;
use Annual\CommissionTask\Transactions\Transaction;

require_once "./vendor/autoload.php";
$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();


$rawData = (new CsvDataReader($_ENV["CSV_URL"]))
    ->setFormatter(new CsvFormatter())
    ->getDataFromFile();

$exchangeRateServiceObj = (new ExchangeRateService($_ENV["EXCHANGE_RATE_URL"]))
    ->setFormatter(new ExchangeRateFormatter());

$commissionManager = new CalculateManager();

$commissionManager
    ->addRule(new DepositRule())
    ->addRule(new WithdrawBusinessRule())
    ->addRule(new WithdrawPrivateRule(new WeeklyMemorization(), $exchangeRateServiceObj));



if (!empty($rawData)) {
    foreach ($rawData as $eachRow) {
        $transaction = $commissionManager
            ->applyAllRulesUsingGenerator(new Transaction($eachRow));
        echo number_format($transaction->getCommission(), 2, '.', '') . "\n";
    }
}
