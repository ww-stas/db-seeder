<?php declare(strict_types=1);

namespace App;

use App\Config\AppConfig;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCommand extends Command
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
     * @throws ReflectionException
     * @throws ValidationException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument('filename');

        if (!file_exists($filename)) {
            $output->writeln("<error>File couldn't be loaded</error>");

            return Command::FAILURE;
        }

        $config = $this->readConfig($filename);
        $seeder = new Seeder($config);
        $seeder->run();

        return Command::SUCCESS;
    }

    /**
     * @throws ReflectionException
     * @throws ValidationException
     */
    private function readConfig(string $filename):AppConfig {
        return ConfigMapper::make()->map(AppConfig::class, $filename);
    }
}
