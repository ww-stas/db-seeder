<?php declare(strict_types=1);

namespace App\Command;

use App\Application;
use App\Console;
use App\Counter;
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
        Metric::start("App");
        $filename = $input->getArgument('filename');

        if (!file_exists($filename)) {
            $output->writeln("<error>File couldn't be loaded</error>");

            return Command::FAILURE;
        }

        Console::init($output);
        Counter::getInstance()->setOutput($output);
        $app = new Application($filename);
        $this->runMethod($app);
        Metric::stop("App");
        Metric::print($output);

        return Command::SUCCESS;
    }
}
