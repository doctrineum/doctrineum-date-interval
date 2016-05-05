<?php
namespace Doctrineum\Tests\DateInterval;

use Doctrineum\DateInterval\DateIntervalToSeconds;
use Herrera\DateInterval\DateInterval as HerreraDateInterval;

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
     * @expectedException \Doctrineum\DateInterval\Exceptions\IntervalToIntegerOverflow
     */
    public function I_can_not_convert_too_high_interval()
    {
        $maxInterval = HerreraDateInterval::fromSeconds(PHP_INT_MAX);
        $overflowingYear = $maxInterval->y + 1;
        $interval = new \DateInterval("P{$overflowingYear}Y");
        DateIntervalToSeconds::toSeconds($interval);
    }
}
