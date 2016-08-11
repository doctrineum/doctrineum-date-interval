<?php
namespace Doctrineum\Tests\DateInterval;

use Doctrineum\DateInterval\HerreraDateInterval;
use PHPUnit_Framework_TestCase as TestCase;

class HerreraDateIntervalTest extends TestCase
{
    public function provideDiffs()
    {
        /**
         * @var \DateTime[][] $list
         */
        $list = [
            [
                '1',
                new \DateTime('2010-01-01 00:00:01'),
                new \DateTime('2010-01-01 00:00:00'),
            ],
            [
                '60',
                new \DateTime('2010-01-01 00:01:00'),
                new \DateTime('2010-01-01 00:00:00'),
            ],
            [
                '3600',
                new \DateTime('2010-01-01 01:00:00'),
                new \DateTime('2010-01-01 00:00:00'),
            ],
            [
                '86400',
                new \DateTime('2010-01-02 00:00:00'),
                new \DateTime('2010-01-01 00:00:00'),
            ],
            [
                null,
                new \DateTime('2010-02-01 00:00:00'),
                new \DateTime('2010-01-01 00:00:00'),
            ],
            [
                null,
                new \DateTime('2011-01-01 00:00:00'),
                new \DateTime('2010-01-01 00:00:00'),
            ],
        ];

        $month = $list[4][1]->diff($list[4][2]);
        $list[4][0] = $month->days * HerreraDateInterval::SECONDS_DAY;

        $year = $list[5][1]->diff($list[5][2]);
        $list[5][0] = $year->days * HerreraDateInterval::SECONDS_DAY;

        return $list;
    }

    public function provideSeconds()
    {
        return [
            ['1', 'PT1S'],
            ['60', 'PT1M'],
            ['3600', 'PT1H'],
            ['86400', 'P1D'],
            ['2629740', 'P1M'],
            ['31556874', 'P1Y'],
            ['34276675', 'P1Y1M1DT1H1M1S'],
            ['2056600500', 'P65Y2M1DT16H3M30S'],
        ];
    }

    public function provideSpec()
    {
        return [
            ['PT1S'],
            ['PT1M'],
            ['PT1H'],
            ['P1D'],
            ['P1M'],
            ['P1Y'],
            ['P1Y1M1DT1H1M1S']
        ];
    }

    /**
     * @dataProvider provideSeconds
     * @param int $seconds
     * @param string $spec
     */
    public function testFromSeconds($seconds, $spec)
    {
        $interval = HerreraDateInterval::fromSeconds($seconds);

        self::assertEquals($spec, $interval->toSpec());
    }

    /**
     * @test
     */
    public function I_can_create_it_from_interval_or_seconds()
    {
        $fromSeconds = HerreraDateInterval::fromSeconds('2056600500');
        $interval = new HerreraDateInterval('P60Y60M60DT60H60M60S');

        self::assertEquals($fromSeconds->toSeconds(), $interval->toSeconds());
    }

    /**
     * @test
     */
    public function I_get_interval_specification_when_turning_to_string()
    {
        self::assertEquals('PT1S', (string)new HerreraDateInterval('PT1S'));
    }

    /**
     * @test
     * @dataProvider provideSeconds
     * @param int $seconds
     * @param string $spec
     */
    public function I_can_convert_it_to_seconds($seconds, $spec)
    {
        $interval = new HerreraDateInterval($spec);

        self::assertEquals($seconds, $interval->toSeconds());
    }

    /**
     * @test
     * @dataProvider provideDiffs
     *
     * @param string $expectedSeconds
     * @param \DateTime $left
     * @param \DateTime $right
     */
    public function I_can_convert_it_to_seconds_using_days($expectedSeconds, \DateTime $left, \DateTime $right)
    {

        self::assertEquals(
            $expectedSeconds,
            HerreraDateInterval::toSecondsUsingDays($left->diff($right))
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The "days" property is not set.
     */
    public function I_can_not_convert_it_to_seconds_from_days_if_has_no_days_at_all()
    {
        HerreraDateInterval::toSecondsUsingDays(new HerreraDateInterval('PT0S'));
    }

    /**
     * @test
     * @dataProvider provideSpec
     * @param string $spec
     */
    public function I_can_convert_it_to_spec($spec)
    {
        $interval = new HerreraDateInterval($spec);

        self::assertEquals($spec, $interval->toSpec());
    }
}