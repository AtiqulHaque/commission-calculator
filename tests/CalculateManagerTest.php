<?php
declare(strict_types=1);

namespace Annual\CommissionTask\Tests;

use Annual\CommissionTask\CalculateManager;
use Annual\CommissionTask\CommissionRules\DepositRule;
use Annual\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Annual\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Annual\CommissionTask\Service\ExchangeRateService\ExchangeRateService;
use Annual\CommissionTask\Service\WeeklyMemorization;
use Annual\CommissionTask\Transactions\CsvInputData;
use Annual\CommissionTask\Transactions\TransactionCollection;
use PHPUnit\Framework\TestCase;
use stdClass;

class CalculateManagerTest extends TestCase
{
    public function testFileReadWhenEmpty()
    {
        $rateService = $this->getMockBuilder(ExchangeRateService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRate'])
            ->getMock();


        $collection = new TransactionCollection([]);

        $rateService->method('getRate')->willReturn(1.00);

        $manager = new CalculateManager();


        $manager->addTransactions($collection)
            ->addRule(new DepositRule())
            ->addRule(new WithdrawBusinessRule())
            ->addRule(new WithdrawPrivateRule(new WeeklyMemorization(), $rateService))
            ->applyAllRules();


        $this->assertEquals(0, count($manager->getAllTransactions()));
    }

    /**
     * @param $tDate
     * @param $user
     * @param $uType
     * @param $opType
     * @param $amount
     * @param $currency
     * @param $commi
     * @param $rate
     * @dataProvider dataProviderForAddTesting
     */
    public function testCalculatorWithValidWithdraw($tDate, $user, $uType, $opType, $amount, $currency, $commi, $rate)
    {
        $rateService = $this->getMockBuilder(ExchangeRateService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRate'])
            ->getMock();

        $collection = new TransactionCollection([
            $this->getSampleTransaction(
                $tDate,
                $user,
                $uType,
                $opType,
                $amount,
                $currency
            )
        ]);

        $rateService->method('getRate')->willReturn($rate);

        $manager = new CalculateManager();


        $manager->addTransactions($collection)
            ->addRule(new DepositRule())
            ->addRule(new WithdrawBusinessRule())
            ->addRule(new WithdrawPrivateRule(new WeeklyMemorization(), $rateService))
            ->applyAllRules();

        $calculateTransaction = $manager->getAllTransactions();
        $this->assertEquals(1, count($calculateTransaction));
        $this->assertEquals($commi, round($calculateTransaction[0]->getCommission(), 2));
    }


    public function testCalculatorWithValidWithdrawWithMultipleTransition()
    {
        $content = array();
        $requiredCommission = array();

        foreach ($this->dataProviderForMultipleTransaction() as $item) {
            $content [] = $this->getSampleTransaction(
                $item[0],
                $item[1],
                $item[2],
                $item[3],
                $item[4],
                $item[5]
            );
            $requiredCommission [] = $item[6];
        }

        $rateService = $this->getMockBuilder(ExchangeRateService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRate'])
            ->getMock();

        $collection = new TransactionCollection($content);

        $rateService->method('getRate')->willReturn(1.00);

        $manager = new CalculateManager();


        $manager->addTransactions($collection)
            ->addRule(new DepositRule())
            ->addRule(new WithdrawBusinessRule())
            ->addRule(new WithdrawPrivateRule(new WeeklyMemorization(), $rateService))
            ->applyAllRules();

        $calculateTransaction = $manager->getAllTransactions();
        $this->assertEquals(3, count($calculateTransaction));

        foreach ($calculateTransaction as $key => $each) {
            $this->assertEquals(
                $requiredCommission[$key],
                $each->getCommission(),
                "Calculate for Date : {$each->getTransactionDate()}"
            );
        }
    }


    public function dataProviderForMultipleTransaction(): array
    {
        return [                       // data sets
            [
                '2014-12-31',
                '4',
                'private',
                "withdraw",
                1200.00,
                "EUR",
                .6
            ],
            [
                '2015-01-01',
                '4',
                'private',
                "withdraw",
                1000.00,
                "EUR",
                3
            ]
            ,
            [
                '2016-01-05',
                '4',
                'private',
                "withdraw",
                1000.00,
                "EUR",
                0
            ]

        ];
    }

    public function dataProviderForAddTesting(): array
    {
        return [
            'add 2 natural numbers'                                               => [
                '2014-12-31',
                '4',
                'private',
                "withdraw",
                1200.00,
                "EUR",
                .6,
                1.00
            ],
            'Calculate Commission with 2015-01-01,4,private,withdraw,1000.00,EUR' => [
                '2015-01-01',
                '4',
                'private',
                "withdraw",
                1000.00,
                "EUR",
                0.0,
                1.00
            ],
            'Calculate Commission with 2016-01-05,4,private,withdraw,1000.00,EUR' => [
                '2016-01-05',
                '4',
                'private',
                "withdraw",
                1000.00,
                "EUR",
                0.0,
                1.00
            ],'Calculate Commission with 2016-01-06,1,private,withdraw,30000,JPY' => [
                '2016-01-05',
                '1',
                'private',
                "withdraw",
                30000,
                "JPY",
                0.0,
                129.53
            ],'Calculate Commission with 2016-02-19,5,private,withdraw,3000000,JPY' => [
                '2016-02-19',
                '5',
                'private',
                "withdraw",
                3000000,
                "JPY",
                8611.41,
                129.53
            ],
        ];
    }

    public function getSampleTransaction($transactionDate, $user, $userType, $opType, $amount, $currency): CsvInputData
    {
        $inputObject = new CsvInputData();
        $inputObject->setTransactionDate($transactionDate);
        $inputObject->setUserIdentification($user);
        $inputObject->setUserType($userType);
        $inputObject->setOperationType($opType);
        $inputObject->setOperationAmount($amount);
        $inputObject->setOperationCurrency($currency);
        return $inputObject;
    }
}
