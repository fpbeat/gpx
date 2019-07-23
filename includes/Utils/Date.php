<?php

namespace MapRoute\Utils;

class Date {

    public static function secondsToTime($inputSeconds, $units = ['day', 'hour', 'minute']) {
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        $days = floor($inputSeconds / $secondsInADay);

        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        $sections = [
            'day' => (int)$days,
            'hour' => (int)$hours,
            'minute' => (int)$minutes,
            'second' => (int)$seconds,
        ];

        $timeParts = [];
        foreach ($sections as $name => $value) {
            if ($value > 0 && in_array($name, $units)) {
                switch ($name) {
                    case 'day':
                        $unit = Text::plural($value, ['день', 'дня', 'дней']);
                        break;
                    case 'hour':
                        $unit = Text::plural($value, ['час', 'часа', 'часов']);
                        break;
                    case 'minute':
                        $unit = Text::plural($value, ['минута', 'минуты', 'минут']);
                        break;
                    case 'second':
                        $unit = Text::plural($value, ['секунда', 'секунды', 'секунд']);
                        break;
                }

                $timeParts[] = sprintf('%s %s', $value, $unit);
            }
        }

        return count($timeParts) > 0 ?implode(' ', $timeParts) : 'меньше минуты';
    }
}