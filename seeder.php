<?php declare(strict_types=1);

use App\Command\PlanCommand;
use App\Command\SeedCommand;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/vendor/autoload.php';

$application = new Application();
$application->add(new SeedCommand());
$application->add(new PlanCommand());
$application->run();
