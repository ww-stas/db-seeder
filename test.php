<?php declare(strict_types=1);

use App\Config\AppConfig;
use App\ConfigMapper;
use App\Seeder;

require_once __DIR__ . '/vendor/autoload.php';

$filename = __DIR__ . '/examples/test-seed.yml';


$config = ConfigMapper::make()->map(AppConfig::class, $filename);
//$t = 1;
//foreach ($config->models as $model) {
//    echo $model->table . ":\n";
//    foreach ($model->columns as $name => $column) {
//        echo " -  $name : {$column->resolve()}\n";
//    }
//}

$seeder = new Seeder($config);
$seeder->run();