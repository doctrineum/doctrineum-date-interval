<?php
namespace Doctrineum\DateInterval\ORM\Query\AST\Functions;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrineum\DateInterval\DBAL\Types\DateIntervalType;
use Doctrineum\DateInterval\DateIntervalToSeconds;
use Herrera\DateInterval\DateInterval;

/**
 * "DATE_INTERVAL" "(" StringPrimary ")"
 *
 * @author Kevin Herrera <kherrera@ebscohost.com>
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
     * @throws \Doctrine\ORM\ORMException
     */
    public static function addSelfToDQL(EntityManager $entityManager)
    {
        $entityManager->getConfiguration()->addCustomDatetimeFunction(
            DateIntervalType::DATE_INTERVAL, // case insensitive in DQL
            get_called_class()
        );
    }

    /**
     * @override
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return DateIntervalToSeconds::toSeconds(new DateInterval($this->intervalSpec->value));
    }

    /**
     * @override
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->intervalSpec = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}