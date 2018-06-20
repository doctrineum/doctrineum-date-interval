<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Doctrineum\DateInterval\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrineum\DateInterval\DateInterval;
use Doctrineum\SelfRegisteringType\AbstractSelfRegisteringType;

/**
 * Stores and retrieves DateInterval instances.
 * Inspired by original @author Kevin Herrera <kherrera@ebscohost.com>
 */
class DateIntervalType extends AbstractSelfRegisteringType
{
    public const DATE_INTERVAL = 'date_interval';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::DATE_INTERVAL;
    }

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return null|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value === null
            ? null
            : DateInterval::intervalToSeconds($value);
    }

    /**
     * @param string|null $value
     * @param AbstractPlatform $platform
     * @return DateInterval|null
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateInterval
    {
        if ($value === null) {
            return null;
        }
        if (!\ctype_digit((string)$value)) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                '^\\d+$'
            );
        }

        return DateInterval::fromSeconds($value);
    }
}