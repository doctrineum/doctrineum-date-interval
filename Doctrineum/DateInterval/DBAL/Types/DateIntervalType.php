<?php
namespace Doctrineum\DateInterval\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrineum\DateInterval\DateIntervalToSeconds;
use Herrera\DateInterval\DateInterval as HerreraDateInterval;

/**
 * Stores and retrieves DateInterval instances.
 *
 * @author Kevin Herrera <kherrera@ebscohost.com>
 */
class DateIntervalType extends Type
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
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @override
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value === null
            ? null
            : DateIntervalToSeconds::toSeconds($value);
    }

    /**
     * @param string $value
     * @param AbstractPlatform $platform
     * @return HerreraDateInterval
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        if (!ctype_digit((string)$value)) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                '^\\d+$'
            );
        }

        return HerreraDateInterval::fromSeconds($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::DATE_INTERVAL;
    }
}