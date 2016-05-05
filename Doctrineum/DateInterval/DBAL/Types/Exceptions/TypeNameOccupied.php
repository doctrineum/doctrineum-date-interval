<?php
namespace Doctrineum\DateInterval\DBAL\Types\Exceptions;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;

class TypeNameOccupied extends DBALException
{
    /**
     * @param string $name
     * @param Type $alreadyRegisteredType
     * @return TypeNameOccupied
     */
    public static function typeNameOccupied($name, Type $alreadyRegisteredType)
    {
        return new self(
            'Under type of name ' . $name .
            ' is already registered different type ' . get_class($alreadyRegisteredType)
        );
    }
}