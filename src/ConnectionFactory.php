<?php declare(strict_types=1);

namespace App;

use App\Config\ConnectionConfig;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionFactory
{
    private static ?ConnectionFactory $instance = null;
    private ?Connection $connection = null;

    private function __construct()
    {
    }

    public static function getInstance(): ConnectionFactory
    {
        if (static::$instance === null) {
            static::$instance = new ConnectionFactory();
        }

        return static::$instance;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function createConnection(ConnectionConfig $config): void
    {
        $connectionParams = [
            'dbname'   => $config->database,
            'user'     => $config->user,
            'password' => $config->password,
            'host'     => $config->host,
            'driver'   => $config->driver,
            'port'     => $config->port,
        ];

        $this->connection = DriverManager::getConnection($connectionParams);
    }

    public function getConnection(): Connection
    {
        if ($this->connection === null) {
            throw new \RuntimeException("Connection isn't created. Use createConnection(connectionConfig) first");
        }

        return $this->connection;
    }
}
