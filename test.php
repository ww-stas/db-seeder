<?php declare(strict_types=1);

use App\Application;

require_once __DIR__ . '/vendor/autoload.php';
$filename = __DIR__ . '/examples/test-seed.yml';

$app = new Application($filename);
$app->plan();
