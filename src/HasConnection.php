<?php declare(strict_types=1);

namespace App;

use Doctrine\DBAL\Connection;

/**
 * This trait shows that resolver need to use database connection
 */
trait HasConnection
{
    private Connection $connection;

    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }
}
