<?php declare(strict_types=1);

namespace App;

interface ConnectionDriver
{
    public function insertMany(string $table, array $records): void;
}
