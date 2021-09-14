<?php declare(strict_types=1);

namespace App;

use PDO;

class Connection implements YamlConfigurable
{
    private DbConfig $config;
    private PDO $connection;

    /**
     * @param DbConfig $config
     */
    public function __construct(DbConfig $config)
    {
        $this->config = $config;
        $this->connect();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    private function connect(): void
    {
        $connectionString = 'mysql:host=' . $this->config->host . ';port=' . $this->config->port . ';dbname=' . $this->config->database;
        $this->connection = new PDO($connectionString, $this->config->user, $this->config->password);
    }
}
