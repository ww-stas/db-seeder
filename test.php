<?php declare(strict_types=1);

use App\Config\AppConfig;
use App\ConfigMapper;
use App\ConnectionFactory;
use App\DoctrineDriver;
use App\Seeder;
use App\TestDriver;

require_once __DIR__ . '/vendor/autoload.php';

$filename = __DIR__ . '/examples/test-seed.yml';
$config = ConfigMapper::make()->mapFromFile(AppConfig::class, $filename);
//$connection = Connection::make($config->getConnectionConfig());
//$connectionFactory = new ConnectionFactory();
$connectionFactory = ConnectionFactory::getInstance();
$connectionFactory->createConnection($config->getConnectionConfig());
$connection = $connectionFactory->getConnection();
$driver = new TestDriver();
$seeder = new Seeder($config, $driver);
$seeder->run();