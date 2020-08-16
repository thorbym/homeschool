<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

class Helper
{
    public static function convertDaysOfWeek(string $daysOfWeekString)
    {
        // first test string to see if it's every day, weekdays or weekend
        if ($daysOfWeekString == '["1","2","3","4","5","6","0"]') {
            return 'Every day';
        }
        if ($daysOfWeekString == '["1","2","3","4","5"]') {
            return 'Weekdays only';
        }
        if ($daysOfWeekString == '["6","0"]') {
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

    public static function getNextEvent(string $daysOfWeekString, string $startTime, string $endTime)
    {
        $currentDay = date('N');
        $currentTime = date('Hi');
        $daysOfWeekArray = json_decode($daysOfWeekString);
        $eventState = self::getEventState($currentTime, $startTime, $endTime);
        if (($eventState == 'notStarted' || $eventState == 'notFinished' ) && in_array($currentDay, $daysOfWeekArray)) {
            // event has not finished, and is on day of request
            return [
                'startTime' => date('Y-m-d\T' . $startTime),
                'endTime' => date('Y-m-d\T' . $endTime)
            ];
        }
        // example [1,2,3] (mon, tue, wed) and current day is 4
        for ($i = 0; $i <=6; $i++) {
            if (isset($daysOfWeekArray[$i]) && $daysOfWeekArray[$i] <= $currentDay) {
                $daysOfWeekArray[$i] = (string)($daysOfWeekArray[$i] + 7);
            }
        }
        // now array has values which are ALL higher than the current day
        asort($daysOfWeekArray);
        foreach ($daysOfWeekArray as $nextDay) {
            break;
        }
        $daysToAddOn = $nextDay - $currentDay;
        return [
            'startTime' => date('Y-m-d\TH:i', strtotime(date('Y-m-d') . $startTime . ' +' . $daysToAddOn . ' day')),
            'endTime' => date('Y-m-d\TH:i', strtotime(date('Y-m-d') . $endTime . ' +' . $daysToAddOn . ' day'))
        ];
    }

    public static function getEventState($currentTime, $startTime, $endTime)
    {
        if ($currentTime < str_replace(':', '', $startTime)) {
            return 'notStarted';
        }
        if ($currentTime < str_replace(':', '', $endTime)) {
            return 'notFinished';
        }
        return false;
    }
}