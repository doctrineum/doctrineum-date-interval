<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Doctrineum\Tests\DateInterval\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\DateInterval\DBAL\Types\DateIntervalType;
use Doctrineum\Tests\SelfRegisteringType\AbstractSelfRegisteringTypeTest;

class DateIntervalTypeTest extends AbstractSelfRegisteringTypeTest
{
    /**
     * @var AbstractPlatform
     */
    protected $platform;

    /**
     * @var DateIntervalType
     */
    protected $type;

    protected function setUp()
    {
        $this->platform = $this->mockery(AbstractPlatform::class);
        DateIntervalType::registerSelf();
        $this->type = Type::getType(DateIntervalType::DATE_INTERVAL);
    }

    protected function tearDown()
    {
        Type::overrideType(DateIntervalType::DATE_INTERVAL, DateIntervalType::class);
    }

    /**
     * @test
     */
    public function I_can_convert_it_to_database_value()
    {
        $interval = new \DateInterval('PT30S');

        self::assertEquals(
            '30',
            $this->type->convertToDatabaseValue($interval, $this->platform)
        );
        self::assertNull(
            $this->type->convertToDatabaseValue(null, $this->platform)
        );
    }

    /**
     * @test
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function I_can_not_convert_invalid_value_from_database_to_php()
    {
        $this->type->convertToPHPValue('abcd', $this->platform);
    }

    /**
     * @test
     */
    public function I_can_convert_database_value_to_interval()
    {
        $interval = $this->type->convertToPHPValue('30', $this->platform);

        self::assertEquals(30, $interval->s);
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    /**
     * @test
     */
    public function It_has_same_SQL_declaration_as_big_int()
    {
        DateIntervalType::registerSelf();
        /** @var DateIntervalType $dateIntervalType */
        $dateIntervalType = Type::getType('date_interval');
        $platform = \Mockery::mock(AbstractPlatform::class);
        $someFieldDeclaration = ['foo'];
        $platform->shouldReceive('getBigIntTypeDeclarationSQL')
            ->with($someFieldDeclaration)
            ->once()
            ->andReturn($bigIntTypeDeclarationSQL = 'bar');
        /** @var AbstractPlatform $platform */
        self::assertSame($bigIntTypeDeclarationSQL, $dateIntervalType->getSQLDeclaration($someFieldDeclaration, $platform));
    }
}