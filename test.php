<?php declare(strict_types=1);

use App\ConfigMapper;
use App\DbConfig;
use App\TestConfig;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/vendor/autoload.php';

//print_r(Yaml::parseFile(__DIR__ . '/examples/example.yml'));


$filename = __DIR__ . '/examples/test-seed.yml';

ConfigMapper::make()->map(TestConfig::class, $filename);