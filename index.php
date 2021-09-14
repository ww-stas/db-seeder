<?php declare(strict_types=1);

use App\Config;
use App\Connection;
use App\DbConfig;
use App\Model\Agency;
use App\Model\Customer;
use App\Seeder;

require_once __DIR__ . '/vendor/autoload.php';

$configFile = __DIR__ . '/config/config.yml';
//$configFile = __DIR__ . '/config/config-local.yml';

$config = new Config($configFile);
$connection = new Connection($config->getConfig('db', DbConfig::class));

$res = Seeder::make($connection)
    ->generate(Agency::class, 10)
    ->forEach(function (Seeder $seeder, Agency $agency) {
        $seeder->generate(Customer::class, 1000, ['agency_id' => $agency->id]);
    });

