<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Doctrineum\DateInterval;

/**
 * @method static DateInterval fromSeconds($seconds)
 * @method static DateInterval createFromDateString($time)
 */
class DateInterval extends \Granam\DateInterval\DateInterval
{
    /**
     * @param \DateInterval $interval
     * @return string Returns number of seconds, but turned to string to avoid information loss if too big (> PHP_INT_MAX)
     */
    public static function intervalToSeconds(\DateInterval $interval): string
    {
        $seconds = $interval->s;
        if ($interval->i > 0) {
            $seconds += $interval->i * self::SECONDS_MINUTE;
        }
        if ($interval->h > 0) {
            $seconds += $interval->h * self::SECONDS_HOUR;
        }
        if ($interval->d > 0) {
            $seconds += $interval->d * self::SECONDS_DAY;
        }
        if ($interval->m > 0) {
            $seconds += $interval->m * self::SECONDS_MONTH;
        }
        if ($interval->y > 0) {
            $yearSeconds = $interval->y * self::SECONDS_YEAR;
            if ($yearSeconds >= 0) {
                $seconds += $yearSeconds;
            } else { // integer overflow
                // fallback with Boston Math calculation (result is in string)
                $seconds = bcadd($seconds, bcmul($interval->y, self::SECONDS_YEAR));
            }
        }

        return (string)$seconds;
    }

    /**
     * Returns the total number of seconds in the interval.
     *
     * @param \DateInterval $interval The date interval.
     * @return string The number of seconds is string (because it can be bigger than PHP_INT_MAX)
     */
    public function toSeconds(\DateInterval $interval = null): string
    {
        if ($interval === null) {
            $interval = $this;
        }

        return static::intervalToSeconds($interval);
    }
}