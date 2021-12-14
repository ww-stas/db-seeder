<?php declare(strict_types=1);

use App\SeedCommand;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/vendor/autoload.php';

$application = new Application();
$command = new SeedCommand();
$application->add($command);
$application->run();
