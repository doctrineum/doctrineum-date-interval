<?php
namespace Doctrineum\DateInterval;

class DateIntervalToSeconds
{

    /**
     * @param \DateInterval $interval
     * @return int|string Returns usually integer, but string if result is too big (> PHP_INT_MAX)
     */
    public static function toSeconds(\DateInterval $interval)
    {
        $seconds = $interval->s;

        if ($interval->i > 0) {
            $seconds += $interval->i * HerreraDateInterval::SECONDS_MINUTE;
        }

        if ($interval->h > 0) {
            $seconds += $interval->h * HerreraDateInterval::SECONDS_HOUR;
        }

        if ($interval->d > 0) {
            $seconds += $interval->d * HerreraDateInterval::SECONDS_DAY;
        }

        if ($interval->m > 0) {
            $seconds += $interval->m * HerreraDateInterval::SECONDS_MONTH;
        }

        if ($interval->y > 0) {
            $yearSeconds = $interval->y * HerreraDateInterval::SECONDS_YEAR;
            if ((int)$yearSeconds >= 0) {
                $seconds += $yearSeconds;
            } else { // integer overflow
                // fallback with Boston Math calculation (result is in string)
                $seconds = bcadd($seconds, bcmul($interval->y, HerreraDateInterval::SECONDS_YEAR));
            }
        }

        return $seconds;
    }
}