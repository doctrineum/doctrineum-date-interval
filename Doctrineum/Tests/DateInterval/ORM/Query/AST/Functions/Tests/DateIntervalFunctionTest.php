<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Doctrineum\Tests\DateInterval\ORM\Query\AST\Functions;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrineum\DateInterval\DBAL\Types\DateIntervalType;
use Doctrineum\DateInterval\ORM\Query\AST\Functions\DateIntervalFunction;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Mapping as ORM;

class DateIntervalFunctionTest extends TestCase
{

    /** @var EntityManager */
    private $entityManager;
    /** @var SQLLogger */
    private $sqlLogger;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setUp(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('The pdo_sqlite extension is not available.');
        }
        $config = Setup::createAnnotationMetadataConfiguration($paths = [__DIR__], true /* dev mode */);
        $cache = new ArrayCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $driver = new ORM\Driver\AnnotationDriver(new AnnotationReader(), $paths);
        $config->setMetadataDriverImpl($driver);
        $this->sqlLogger = new DebugStack();
        $config->setSQLLogger($this->sqlLogger);
        $this->entityManager = EntityManager::create(['driver' => 'pdo_sqlite', 'path' => ':memory:'], $config);
        DateIntervalFunction::addSelfToDQL($this->entityManager);
        DateIntervalType::registerSelf();
    }

    /**
     * @test
     */
    public function I_can_use_date_interval_in_dql(): void
    {
        $query = $this->entityManager->createQuery(str_replace(
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
     * @var int|null
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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateInterval|null
     */
    public function getInterval(): ?\DateInterval
    {
        return $this->interval;
    }

    /**
     * @param \DateInterval $interval
     */
    public function setInterval(\DateInterval $interval): void
    {
        $this->interval = $interval;
    }
}