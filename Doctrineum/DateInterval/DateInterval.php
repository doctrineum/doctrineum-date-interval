<?php
namespace Doctrineum\DateInterval;

use Herrera\DateInterval\DateInterval as HerreraDateInterval;

/**
 * @method static DateInterval fromSeconds($seconds)
 * @method static DateInterval createFromDateString($time)
 */
class DateInterval extends HerreraDateInterval
{
    /**
     * @param \DateInterval $interval
     * @return int|string Returns usually integer, but string if result is too big (> PHP_INT_MAX)
     */
    public static function intervalToSeconds(\DateInterval $interval)
    {
        $seconds = $interval->s;

        if ($interval->i > 0) {
            $seconds += $interval->i * DateInterval::SECONDS_MINUTE;
        }

        if ($interval->h > 0) {
            $seconds += $interval->h * DateInterval::SECONDS_HOUR;
        }

        if ($interval->d > 0) {
            $seconds += $interval->d * DateInterval::SECONDS_DAY;
        }

        if ($interval->m > 0) {
            $seconds += $interval->m * DateInterval::SECONDS_MONTH;
        }

        if ($interval->y > 0) {
            $yearSeconds = $interval->y * DateInterval::SECONDS_YEAR;
            if ((int)$yearSeconds >= 0) {
                $seconds += $yearSeconds;
            } else { // integer overflow
                // fallback with Boston Math calculation (result is in string)
                $seconds = bcadd($seconds, bcmul($interval->y, DateInterval::SECONDS_YEAR));
            }
        }

        return $seconds;
    }

    /**
     * Returns the total number of seconds in the interval.
     *
     * @param \DateInterval $interval The date interval.
     *
     * @return string|int The number of seconds.
     */
    public function toSeconds(\DateInterval $interval = null)
    {
        if ($interval === null) {
            $interval = $this;
        }

        return static::intervalToSeconds($interval);
    }
}