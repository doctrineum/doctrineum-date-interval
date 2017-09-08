<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Doctrineum\Tests\DateInterval;

use Doctrineum\DateInterval\DateInterval as DoctrineumDateInterval;

include_once __DIR__ . '/../../../vendor/granam/date-interval/Granam/Tests/DateInterval/DateIntervalTest.php';

class DateIntervalTest extends \Granam\Tests\DateInterval\DateIntervalTest
{
    /**
     * @test
     * @dataProvider provideIntervalSpecification
     * @param string|int $expectedSeconds
     * @param string $intervalSpecification
     */
    public function I_can_convert_interval_to_seconds($expectedSeconds, string $intervalSpecification)
    {
        $expectedSeconds = (String)$expectedSeconds;
        $interval = new \DateInterval($intervalSpecification);
        self::assertSame($expectedSeconds, $inSeconds = DoctrineumDateInterval::intervalToSeconds($interval));
        self::assertInternalType('string', $inSeconds);

        $doctrineumInterval = new DoctrineumDateInterval($intervalSpecification);
        self::assertSame($expectedSeconds, $inSeconds = $doctrineumInterval->toSeconds());
        self::assertInternalType('string', $inSeconds);
    }

    public function provideIntervalSpecification(): array
    {
        return [
            ['1', 'PT1S'],
            [31556874 + 2 * 2629740 + 3600 + 45, 'P1Y2MT1H45S'], // 60 3600 86400 2629740 31556874
            [2016 * 31556874 + 5 * 2629740 + 5 * 86400 + 7 * 3600 + 53 * 60 + 34, 'P2016Y5M5DT7H53M34S'],
        ];
    }

    /**
     * @test
     */
    public function I_can_convert_to_seconds_even_if_higher_than_max_integer()
    {
        $maxInterval = DoctrineumDateInterval::fromSeconds(PHP_INT_MAX);
        $overflowingYear = $maxInterval->y + 1;

        $interval = new \DateInterval("P{$overflowingYear}Y");
        $inSeconds = DoctrineumDateInterval::intervalToSeconds($interval);
        self::assertInternalType('string', $inSeconds);
        self::assertGreaterThan(PHP_INT_MAX, $inSeconds);

        $doctrineumInterval = new DoctrineumDateInterval("P{$overflowingYear}Y");
        $inSeconds = $doctrineumInterval->toSeconds();
        self::assertInternalType('string', $inSeconds);
        self::assertGreaterThan(PHP_INT_MAX, $inSeconds);
    }
}