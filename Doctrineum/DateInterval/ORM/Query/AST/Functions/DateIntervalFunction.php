<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Doctrineum\DateInterval\ORM\Query\AST\Functions;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrineum\DateInterval\DBAL\Types\DateIntervalType;
use Doctrineum\DateInterval\DateInterval;

/**
 * "DATE_INTERVAL" "(" StringPrimary ")"
 * Inspired by original @author Kevin Herrera <kherrera@ebscohost.com>
 */
class DateIntervalFunction extends FunctionNode
{
    /**
     * The extracted date interval specification.
     *
     * @var Literal
     */
    private $intervalSpec;

    /**
     * Use as for example "foo.bar < DATE_INTERVAL('PT1H')" in your DQL (interval is always converted to seconds)
     *
     * @param EntityManager $entityManager
     */
    public static function addSelfToDQL(EntityManager $entityManager): void
    {
        $entityManager->getConfiguration()->addCustomDatetimeFunction(
            DateIntervalType::DATE_INTERVAL, // case insensitive in DQL
            static::class
        );
    }

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return DateInterval::intervalToSeconds(new DateInterval($this->intervalSpec->value));
    }

    /**
     * @param Parser $parser
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->intervalSpec = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}