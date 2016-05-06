<?php
namespace Doctrineum\DateInterval\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrineum\DateInterval\DateIntervalToSeconds;
use Doctrineum\SelfRegisteringType\AbstractSelfRegisteringType;
use Herrera\DateInterval\DateInterval as HerreraDateInterval;

/**
 * Stores and retrieves DateInterval instances.
 *
 * Inspired by original @author Kevin Herrera <kherrera@ebscohost.com>
 */
class DateIntervalType extends AbstractSelfRegisteringType
{
    const DATE_INTERVAL = 'date_interval';

    /**
     * @return string
     */
    public function getName()
    {
        return self::DATE_INTERVAL;
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

}