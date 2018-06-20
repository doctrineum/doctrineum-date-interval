<?php
$loader = require __DIR__ . '/../../vendor/autoload.php';
$loader->add('Doctrine\\Tests', __DIR__ . '/../../vendor/doctrine/dbal/tests');
$loader->add('Doctrine\\Tests', __DIR__ . '/../../vendor/doctrine/orm/tests');

\error_reporting(-1);
\ini_set('display_errors', '1');
\ini_set('xdebug.max_nesting_level', '100');
