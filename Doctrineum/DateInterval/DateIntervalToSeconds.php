<?php
namespace Doctrineum\DateInterval;

use Herrera\DateInterval\DateInterval as HerreraDateInterval;

class DateIntervalToSeconds
{

    /**
     * @param \DateInterval $interval
     * @return int
     * @throws \Doctrineum\DateInterval\Exceptions\IntervalToIntegerOverflow
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
            if ((int)$yearSeconds < 0 || !is_int($yearSeconds)) {
                throw new Exceptions\IntervalToIntegerOverflow(
                    'Given interval is too high to convert into integer. Years as int resulted into '
                    . '('  . gettype($yearSeconds) . ') ' . var_export($yearSeconds, true)
                    . ' which is ' . var_export((int)$yearSeconds, true) . ' as integer'
                );
            }
            $seconds += $yearSeconds;
        }

        return $seconds;
    }
}