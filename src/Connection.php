<?php declare(strict_types=1);

namespace App;

use App\Config\ConnectionConfig;
use Doctrine\DBAL\DriverManager;
use PDO;

abstract class Connection
{
    protected PDO $connection;
    protected ConnectionConfig $config;

    /**
     * @param ConnectionConfig $config
     */
    public function __construct(ConnectionConfig $config)
    {
        $this->config = $config;
        $this->connect();
    }


    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public static function make(ConnectionConfig $config): \Doctrine\DBAL\Connection
    {
        $connectionParams = [
            'dbname' => $config->database,
            'user' => $config->user,
            'password' => $config->password,
            'host' => $config->host,
            'driver' => $config->driver,
            'port' => $config->port
        ];

        return DriverManager::getConnection($connectionParams);
    }


    public function getConnection(): PDO
    {
        return $this->connection;
    }

    abstract protected function getConnectionString(): string;

    protected function connect(): void
    {
        $this->connection = new PDO(
            $this->getConnectionString(),
            $this->config->user,
            $this->config->password
        );
    }
}
