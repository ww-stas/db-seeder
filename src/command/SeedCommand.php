<?php declare(strict_types=1);

namespace App\Command;

use App\Application;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Input\InputArgument;

class SeedCommand extends AppCommand
{
    protected static $defaultName = 'seed:run';

    protected function configure(): void
    {
        $this
            ->setDescription('Runs a seed with given configuration')
            ->setHelp('Runs a seed with given configuration')
            ->addArgument('filename', InputArgument::REQUIRED, 'Path to the configuration file');
    }

    /**
     * @throws Exception
     */
    protected function runMethod(Application $application): void
    {
        $application->start();
    }
}
