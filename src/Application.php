<?php declare(strict_types=1);

namespace App;

use App\Config\AppConfig;
use App\Context\Context;
use App\Mapper\ConfigMapper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use ReflectionException;

class Application
{
    private ?AppConfig $config = null;
    private ?Connection $connection = null;

    /**
     * @throws ReflectionException
     * @throws ValidationException
     * @throws Exception
     */
    public function __construct(string $configFile)
    {
        $this->init($configFile);
    }

    /**
     * Start seed plan.
     *
     * @throws Exception
     */
    public function start(): void
    {
        $this->runSeed(new DoctrineDriver($this->connection));
        Metric::summary();
    }

    /**
     * Runs a seed in a plan mode. This mode will generate seed plan and displays it.
     *
     * @throws Exception
     */
    public function plan(): void
    {
        $testDriver = new TestDriver();
        $this->runSeed($testDriver);
        Console::message("");
        Console::message("Execution plan is:");
        foreach ($testDriver->getInserts() as $table => $insert) {
            Console::message(" -$table - $insert");
        }
    }

    /**
     * Deletes all records for tables described in the `seed` section of config.
     */
    public function clear(): void
    {
    }

    /**
     * @throws Exception
     */
    public function exceptionHandler($exception): void
    {
        if ($this->connection !== null && $this->connection->isTransactionActive()) {
            $this->connection->rollBack();
        }

        if ($exception instanceof AppException) {
            Console::message($exception->getErrorMessage());
            exit(1);
        }

        throw $exception;
    }

    /**
     * @throws Exception
     */
    private function runSeed(ConnectionDriver $connectionDriver): void
    {
        $this->connection->beginTransaction();
        $context = new Context($this->config, $connectionDriver, new InMemoryCache());
        $context->scan();
        $seeder = new Seeder($this->config, $connectionDriver);
        $seeder->run();
        $this->connection->commit();
    }

    /**
     * @throws ReflectionException
     * @throws ValidationException
     * @throws Exception
     */
    private function init(string $configFile): void
    {
        set_exception_handler([$this, 'exceptionHandler']);
        $this->createConfig($configFile);
        $this->createConnection();
    }

    /**
     * @throws ReflectionException
     * @throws ValidationException
     */
    private function createConfig(string $configFile): void
    {
        $this->config = ConfigMapper::make()->mapFromFile(AppConfig::class, $configFile);
    }

    /**
     * @throws Exception
     */
    private function createConnection(): void
    {
        $connectionFactory = ConnectionFactory::getInstance();
        $connectionFactory->createConnection($this->config->getConnectionConfig());
        $this->connection = $connectionFactory->getConnection();
    }
}
