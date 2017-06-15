# Doctrine DateInterval Type

[![Build Status](https://travis-ci.org/jaroslavtyc/doctrineum-date-interval.png?branch=master)](https://travis-ci.org/jaroslavtyc/doctrineum-date-interval)
[![Test Coverage](https://codeclimate.com/github/jaroslavtyc/doctrineum-date-interval/badges/coverage.svg)](https://codeclimate.com/github/jaroslavtyc/doctrineum-date-interval/coverage)
[![License](https://poser.pugx.org/doctrineum/date-interval/license)](https://packagist.org/packages/doctrineum/date-interval)

Adds `DateInterval` to Doctrine ORM (can be used as a `@Column(type="date-interval")`) and DBAL (can be used in DQL queries as `DATE_INTERVAL`).

## Usage

```php
<?php
namespace ChopChop;

use Doctrine\ORM\Mapping as ORM;
use \Granam\DateInterval\DateInterval as GranamDateInterval;


/**
 * @ORM\Entity()
 */
class Job
{
    /**
     * @var int
     * @ORM\Id() @ORM\GeneratedValue(strategy="AUTO") @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var GranamDateInterval
     * @ORM\Column(type="date_interval")
     */
    private $interval;

    /**
     * @return GranamDateInterval
     */
    public function getInterval(): GranamDateInterval
    {
        return $this->interval;
    }

    /**
     * @param GranamDateInterval $interval
     */
    public function setInterval(GranamDateInterval $interval)
    {
        $this->interval = $interval;
    }
}

$annualJob = new Job();
$annualJob->setInterval(new GranamDateInterval('P1Y'));

$monthlyJob = new Job();
$monthlyJob->setInterval(new GranamDateInterval('P1M'));

$dailyJob = new Job();
$dailyJob->setInterval(new GranamDateInterval('P1D'));

/** @var \Doctrine\ORM\EntityManager $entityManager */
$entityManager->persist($annualJob);
$entityManager->persist($monthlyJob);
$entityManager->persist($dailyJob);
$entityManager->flush();
$entityManager->clear();

/** @var Job[] $jobs */
$jobs = $entityManager->createQuery(
    "SELECT j FROM Jobs j WHERE j.interval < DATE_INTERVAL('P1Y') ORDER BY j.interval ASC"
)->getResult();

echo $jobs[0]->getInterval()->toSpec(); // "P1D";
echo $jobs[1]->getInterval()->toSpec(); // "P1M"
// note: to spec conversion is feature of HerreraDateInterval
```

## Installation

Add it to your list of Composer dependencies (or by manual edit your composer.json, the `require` section)

```sh
composer require jaroslavtyc/doctrineum-date-interval
```

Register new DBAL type:

```php
<?php

use Doctrineum\DateInterval\DBAL\Types\DateIntervalType;

DateIntervalType::registerSelf();
```

Register new Doctrine ORM function:

```php
<?php

use Doctrineum\DateInterval\ORM\Query\AST\Functions\DateIntervalFunction;
// ... $entityManager = ...
/** @var \Doctrine\ORM\EntityManager $entityManager */
DateIntervalFunction::addSelfToDQL($entityManager);
```

When using Symfony with Doctrine you can do the same as above by configuration:

```yaml
# app/config/config.yml

# Doctrine Configuration
doctrine:
    dbal:
        # ...
        mapping_types:
            date_interval: date_interval
        types:
            date_interval: Doctrineum\DateInterval\DBAL\Types\DateIntervalType
    orm:
        # ...
        dql:
            datetime_functions:
                DATE_INTERVAL: Doctrineum\DateInterval\ORM\Query\AST\Functions\DateIntervalFunction
```
