<?php
namespace Doctrineum\Tests\DateInterval;

use Doctrineum\DateInterval\DateIntervalToSeconds;
use Doctrineum\DateInterval\HerreraDateInterval;

class DateIntervalToSecondsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider provideIntervalSpecification
     * @param int $expectedSeconds
     * @param string $intervalSpecification
     */
    public function I_can_convert_interval_to_seconds($expectedSeconds, $intervalSpecification)
    {
        $interval = new \DateInterval($intervalSpecification);
        self::assertSame($expectedSeconds, $inSeconds = DateIntervalToSeconds::toSeconds($interval));
        self::assertInternalType('int', $inSeconds);
    }

    public function provideIntervalSpecification()
    {
        return [
            [1, 'PT1S'],
            [31556874 + 2 * 2629740 + 3600 + 45, 'P1Y2MT1H45S'], // 60 3600 86400 2629740 31556874
            [2016 * 31556874 + 5 * 2629740 + 5 * 86400 + 7 * 3600 + 53 * 60 + 34, 'P2016Y5M5DT7H53M34S'],
        ];
    }

    /**
     * @test
     */
    public function I_can_convert_to_seconds_even_if_higher_than_max_integer()
    {
        $maxInterval = HerreraDateInterval::fromSeconds(PHP_INT_MAX);
        $overflowingYear = $maxInterval->y + 1;
        $interval = new \DateInterval("P{$overflowingYear}Y");
        $inSeconds = DateIntervalToSeconds::toSeconds($interval);
        self::assertInternalType('string', $inSeconds);
        self::assertGreaterThan(PHP_INT_MAX, $inSeconds);
    }
}
