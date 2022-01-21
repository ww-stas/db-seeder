<?php declare(strict_types=1);

namespace App\Command;

use App\Application;
use App\Metric;
use App\ValidationException;
use Doctrine\DBAL\Exception;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AppCommand extends Command
{
    abstract protected function runMethod(Application $application): void;

    /**
     * @throws ReflectionException
     * @throws ValidationException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument('filename');

        if (!file_exists($filename)) {
            $output->writeln("<error>File couldn't be loaded</error>");

            return Command::FAILURE;
        }

        $app = new Application($filename);
        $this->runMethod($app);
        Metric::print($output);

        return Command::SUCCESS;
    }
}
