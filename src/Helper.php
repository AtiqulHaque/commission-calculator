<?php

declare(strict_types=1);

namespace Annual\CommissionTask;

class Helper
{
    /**
     * @param $date
     *
     * @return float|int
     */
    public static function calculateWeek($date)
    {
        // 1. Convert input to $year, $month, $day
        $dateSet = strtotime($date);
        $year = date('Y', $dateSet);
        $month = date('m', $dateSet);
        $day = date('d', $dateSet);

        // 2. check if $year is a  leapYear
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $leapYear = true;
        } else {
            $leapYear = false;
        }

        // 3. check if $year-1 is a  leapYear
        if ((($year - 1) % 4 === 0 && ($year - 1) % 100 !== 0) || ($year - 1) % 400 === 0) {
            $leapYearPrev = true;
        } else {
            $leapYearPrev = false;
        }

        // 4. find the dayOfYearNumber for y m d
        $monthNumber = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $dayOfYearNumber = $day + $monthNumber[$month - 1];
        if ($leapYear && $month > 2) {
            ++$dayOfYearNumber;
        }

        // 5. find the jan1weekday for y (monday=1, sunday=7)
        $yy = ($year - 1) % 100;
        $c = ($year - 1) - $yy;
        $g = $yy + intval($yy / 4);
        $janFirstWeekDay = 1 + ((((intval($c / 100) % 4) * 5) + $g) % 7);

        // 6. find the weekday for y m d
        $h = $dayOfYearNumber + ($janFirstWeekDay - 1);
        $weekday = 1 + (($h - 1) % 7);

        // 7. find if y m d falls in yearNumber y-1, weekNumber 52 or 53
        $foundWeekNum = false;
        $weekNumber = 0;
        if ($dayOfYearNumber <= (8 - $janFirstWeekDay) && $janFirstWeekDay > 4) {
            $yearNumber = $year - 1;
            if ($janFirstWeekDay = 5 || ($janFirstWeekDay = 6 && $leapYearPrev)) {
                $weekNumber = 53;
            } else {
                $weekNumber = 52;
            }
            $foundWeekNum = true;
        } else {
            $yearNumber = $year;
        }

        // 8. find if y m d falls in yearNumber y+1, weekNumber 1
        if ($yearNumber === $year && !$foundWeekNum) {
            if ($leapYear) {
                $i = 366;
            } else {
                $i = 365;
            }
            if (($i - $dayOfYearNumber) < (4 - $weekday)) {
                $yearNumber = $year + 1;
                $weekNumber = 1;
                $foundWeekNum = true;
            }
        }

        // 9. find if y m d falls in yearNumber y, weekNumber 1 through 53
        if ($yearNumber === $year && !$foundWeekNum) {
            $j = $dayOfYearNumber + (7 - $weekday) + ($janFirstWeekDay - 1);
            $weekNumber = intval($j / 7);
            if ($janFirstWeekDay > 4) {
                --$weekNumber;
            }
        }

        // 10. output iso week number (YYWW)
        return ($yearNumber - 2000) * 100 + $weekNumber;
    }
}
