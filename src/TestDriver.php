<?php declare(strict_types=1);

namespace App;

class TestDriver implements ConnectionDriver
{
    /**
     * @param Model[] $models
     */
    public function insertMany(array $models): void
    {
        foreach ($models as $model) {
            echo "\t - " . $model . "\n";
        }
    }
}
