<?php declare(strict_types=1);

namespace App;

class MySqlConnection extends Connection
{
    protected function getConnectionString(): string
    {
        $port = $this->config->port ?? '3306';

        return "mysql:host={$this->config->host};port=$port;dbname={$this->config->database}";
    }
}
