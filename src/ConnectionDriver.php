<?php declare(strict_types=1);

namespace App;

interface ConnectionDriver
{
    /**
     * @param Model[] $models
     */
    public function insertMany(array $models): void;

    public function selectRandom(string $model): array;
}
