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

    /**
     * @return string
     */
    public function getMemoryIndex()
    {
        return $index = "{$this->getUserIdentification()}:" .
            "{$this->calcweek($this->getTransactionDate()->format("Y-m-d"))}";
    }

    /**
     * @return mixed
     */
    public function getUserIdentification()
    {
        return $this->userIdentification;
    }

    public function calcweek($date)
    {
        // 1. Convert input to $year, $month, $day
        $dateset = strtotime($date);
        $year = date("Y", $dateset);
        $month = date("m", $dateset);
        $day = date("d", $dateset);


        // 2. check if $year is a  leapyear
        if (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0) {
            $leapyear = true;
        } else {
            $leapyear = false;
        }

        // 3. check if $year-1 is a  leapyear
        if ((($year - 1) % 4 == 0 && ($year - 1) % 100 != 0) || ($year - 1) % 400 == 0) {
            $leapyearprev = true;
        } else {
            $leapyearprev = false;
        }

        // 4. find the dayofyearnumber for y m d
        $mnth = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
        $dayofyearnumber = $day + $mnth[$month - 1];
        if ($leapyear && $month > 2) {
            $dayofyearnumber++;
        }

        // 5. find the jan1weekday for y (monday=1, sunday=7)
        $yy = ($year - 1) % 100;
        $c = ($year - 1) - $yy;
        $g = $yy + intval($yy / 4);
        $jan1weekday = 1 + ((((intval($c / 100) % 4) * 5) + $g) % 7);

        // 6. find the weekday for y m d
        $h = $dayofyearnumber + ($jan1weekday - 1);
        $weekday = 1 + (($h - 1) % 7);

        // 7. find if y m d falls in yearnumber y-1, weeknumber 52 or 53
        $foundweeknum = false;
        if ($dayofyearnumber <= (8 - $jan1weekday) && $jan1weekday > 4) {
            $yearnumber = $year - 1;
            if ($jan1weekday = 5 || ($jan1weekday = 6 && $leapyearprev)) {
                $weeknumber = 53;
            } else {
                $weeknumber = 52;
            }
            $foundweeknum = true;
        } else {
            $yearnumber = $year;
        }

        // 8. find if y m d falls in yearnumber y+1, weeknumber 1
        if ($yearnumber == $year && !$foundweeknum) {
            if ($leapyear) {
                $i = 366;
            } else {
                $i = 365;
            }
            if (($i - $dayofyearnumber) < (4 - $weekday)) {
                $yearnumber = $year + 1;
                $weeknumber = 1;
                $foundweeknum = true;
            }
        }

        // 9. find if y m d falls in yearnumber y, weeknumber 1 through 53
        if ($yearnumber == $year && !$foundweeknum) {
            $j = $dayofyearnumber + (7 - $weekday) + ($jan1weekday - 1);
            $weeknumber = intval($j / 7);
            if ($jan1weekday > 4) {
                $weeknumber--;
            }
        }

        // 10. output iso week number (YYWW)
        return ($yearnumber - 2000) * 100 + $weeknumber;
    }

}
