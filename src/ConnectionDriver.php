<?php declare(strict_types=1);

namespace App;

use App\Config\ModelConfig;

interface ConnectionDriver
{
    /**
     * @param Model[] $models
     */
    public function insertMany(array $models): void;

    public function select(ModelConfig $model, array $condition): array;

    public function isTableExists(string $table): bool;
}
