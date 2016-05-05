<?php
namespace Doctrineum\Tests\DateInterval\DBAL\Types;

use DateInterval;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\Type;
use Doctrine\Tests\DBAL\Mocks\MockPlatform;
use Doctrine\Tests\DbalTestCase;
use Doctrineum\DateInterval\DBAL\Types\DateIntervalType;

class DateIntervalTypeTest extends DbalTestCase
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
        $this->platform = new MockPlatform();
        DateIntervalType::registerSelf();
        $this->type = Type::getType(DateIntervalType::DATE_INTERVAL);
    }

    protected function tearDown()
    {
        Type::overrideType(DateIntervalType::DATE_INTERVAL, DateIntervalType::class);
    }

    public function testConvertToDatabaseValue()
    {
        $interval = new DateInterval('PT30S');

        self::assertEquals(
            '30',
            $this->type->convertToDatabaseValue($interval, $this->platform)
        );
        self::assertNull(
            $this->type->convertToDatabaseValue(null, $this->platform)
        );
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testConvertToPHPValueInvalid()
    {
        $this->type->convertToPHPValue('abcd', $this->platform);
    }

    public function testConvertToPHPValue()
    {
        $interval = $this->type->convertToPHPValue('30', $this->platform);

        self::assertEquals(30, $interval->s);
        self::assertNull(
            $this->type->convertToPHPValue(null, $this->platform)
        );
    }

    /**
     * @test
     * @expectedException \Doctrineum\DateInterval\DBAL\Types\Exceptions\TypeNameOccupied
     */
    public function I_can_not_register_it_by_self_if_name_is_occupied()
    {
        Type::overrideType(DateIntervalType::DATE_INTERVAL, BigIntType::class);
        DateIntervalType::registerSelf();
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