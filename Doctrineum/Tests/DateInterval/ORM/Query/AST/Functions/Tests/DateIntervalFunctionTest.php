<?php
namespace Doctrineum\Tests\DateInterval\ORM\Query\AST\Functions;

use DateInterval;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrineum\DateInterval\DBAL\Types\DateIntervalType;
use Doctrineum\DateInterval\ORM\Query\AST\Functions\DateIntervalFunction;
use PHPUnit_Framework_TestCase;
use Doctrine\ORM\Mapping as ORM;

class DateIntervalFunctionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SQLLogger
     */
    private $sqlLogger;

    protected function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('The pdo_sqlite extension is not available.');
        }

        $config = Setup::createAnnotationMetadataConfiguration(
            $paths = [__DIR__],
            true /* dev mode */
        );
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(
            new \Doctrine\Common\Annotations\AnnotationReader(),
            $paths
        );
        $config->setMetadataDriverImpl($driver);
        $this->sqlLogger = new DebugStack();
        $config->setSQLLogger($this->sqlLogger);
        $this->em = EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'path' => ':memory:',
            ],
            $config
        );

        DateIntervalFunction::addSelfToDQL($this->em);
        DateIntervalType::registerSelf();
    }

    /**
     * @test
     */
    public function I_can_use_date_interval_in_dql()
    {
        $query = $this->em->createQuery(str_replace(
            '%s',
            __NAMESPACE__,
            <<<DQL
SELECT j FROM %s\Job j WHERE j.interval < DATE_INTERVAL('PT1H')
DQL
        ));

        self::assertEquals(
            <<<DQL
SELECT j0_.id AS id_0, j0_.interval AS interval_1 FROM Job j0_ WHERE j0_.interval < 3600
DQL
            ,
            $query->getSQL()
        );
    }
}

/**
 * @ORM\Entity()
 * @ORM\Table(name="Job")
 */
class Job
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var \DateInterval
     * @ORM\Column(type="date_interval")
     */
    private $interval;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateInterval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param DateInterval $interval
     */
    public function setInterval(DateInterval $interval)
    {
        $this->interval = $interval;
    }
}