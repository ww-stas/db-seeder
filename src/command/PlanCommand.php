<?php declare(strict_types=1);

namespace App\Command;

use App\Application;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Input\InputArgument;

class PlanCommand extends AppCommand
{
    protected static $defaultName = 'seed:plan';

    protected function configure(): void
    {
        $this
            ->setDescription('Shows a seed plan with given configuration')
            ->setHelp('Shows a seed plan with given configuration')
            ->addArgument('filename', InputArgument::REQUIRED, 'Path to the configuration file');
    }

    /**
     * @throws Exception
     */
    protected function runMethod(Application $application): void
    {
        $application->plan();
    }
}
