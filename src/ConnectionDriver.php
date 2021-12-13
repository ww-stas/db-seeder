<?php declare(strict_types=1);

namespace App;

interface ConnectionDriver
{
    /**
     * @param string   $table
     * @param Record[] $records
     */
    public function insertMany(string $table, array $records): void;
}
