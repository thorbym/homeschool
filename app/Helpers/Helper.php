<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

class Helper
{
    public static function convertDaysOfWeek(string $daysOfWeekString)
    {
        // first test string to see if it's every day, weekdays or weekend
        if ($daysOfWeekString == '["1", "2", "3", "4", "5", "6", "0"]') {
            return 'Every day';
        }
        if ($daysOfWeekString == '["1", "2", "3", "4", "5"]') {
            return 'Weekdays only';
        }
        if ($daysOfWeekString == '["6", "0"]') {
            return 'Weekends only';
        }
        // string didn't match above cases, so check days and return string
        return self::convertStringToDays($daysOfWeekString);
    }

    public static function convertStringToDays(string $daysOfWeekString)
    {

        $daysOfWeekArray = json_decode($daysOfWeekString);
        $numberOfDays = sizeof($daysOfWeekArray);
        if ($numberOfDays == 1) {
            return self::convertDayNumberToText($daysOfWeekArray[0]);
        }
        $string = "";
        $loopNumber = 0;
        foreach ($daysOfWeekArray as $dayNumber) {
            $loopNumber++;
            $dayName = self::convertDayNumberToText($dayNumber);
            // TODO put in proper error handling
            if (!$dayName) {
                return false;
            }
            if ($loopNumber == 1) {
                $string .= $dayName;
                continue;
            }
            if ($numberOfDays == $loopNumber) {
                $string .= " and " .$dayName;
                continue;
            }
            $string .= ", " . $dayName;
        }
        return $string;
    }

    public static function convertDayNumberToText(int $dayNumber)
    {
        if ($dayNumber > 6) {
            return false;
        }

        $weekDays = [
            0 => 'Sun',
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat'
        ];

        return $weekDays[$dayNumber];
    }
}