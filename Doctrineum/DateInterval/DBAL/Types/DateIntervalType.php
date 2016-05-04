<?php
namespace Doctrineum\DateInterval\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrineum\DateInterval\ToSeconds;
use Herrera\DateInterval\DateInterval as HerreraDateInterval;

/**
 * Stores and retrieves DateInterval instances.
 *
 * @author Kevin Herrera <kherrera@ebscohost.com>
 */
class DateIntervalType extends BigIntType
{
    const DATE_INTERVAL = 'date_interval';

    /**
     * @return bool
     * @throws Exceptions\TypeNameOccupied
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function registerSelf()
    {
        if (static::hasType(self::DATE_INTERVAL)) {
            $alreadyRegisteredType = static::getType(self::DATE_INTERVAL);
            if (get_class($alreadyRegisteredType) !== get_called_class()) {
                throw Exceptions\TypeNameOccupied::typeNameOccupied(self::DATE_INTERVAL, $alreadyRegisteredType);
            }

            return false;
        }

        static::addType(self::DATE_INTERVAL, get_called_class());

        return true;
    }

    /**
     * @override
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value === null
            ? null
            : ToSeconds::toSeconds($value);
    }


    /**
     * @override
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value !== null) {
            if (!ctype_digit((string)$value)) {
                throw ConversionException::conversionFailedFormat(
                    $value,
                    $this->getName(),
                    '^\\d+$'
                );
            }

            $value = HerreraDateInterval::fromSeconds($value);
        }

        return $value;
    }

    /**
     * @override
     */
    public function getName()
    {
        return self::DATE_INTERVAL;
    }
}