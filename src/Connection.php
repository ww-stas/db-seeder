<?php declare(strict_types=1);

namespace App;

use App\Config\ConnectionConfig;
use Doctrine\DBAL\DriverManager;

class Connection
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public static function make(ConnectionConfig $config): \Doctrine\DBAL\Connection
    {
        $connectionParams = [
            'dbname'   => $config->database,
            'user'     => $config->user,
            'password' => $config->password,
            'host'     => $config->host,
            'driver'   => $config->driver,
            'port'     => $config->port,
        ];

        return DriverManager::getConnection($connectionParams);
    }
}
