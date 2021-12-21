<?php declare(strict_types=1);

namespace App;

use App\Config\AppConfig;
use App\Config\ModelConfig;
use App\Config\SeedConfig;
use Doctrine\DBAL\Connection;

class Seeder
{
    private AppConfig $appConfig;
    private Connection $connection;

    /**
     * @param AppConfig        $appConfig
     * @param Connection $connection
     */
    public function __construct(AppConfig $appConfig, Connection $connection)
    {
        $this->appConfig = $appConfig;
        $this->connection = $connection;
    }

    public function run(): void
    {
        foreach ($this->appConfig->seed as $seedConfig) {
            $this->seed($seedConfig);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function seed(SeedConfig $seedConfig, Model $parentModel = null): void
    {
        $modelConfig = $this->findModelConfig($seedConfig->model);

        $generator = new ModelGenerator($modelConfig);
        $models = $generator->generateMany($seedConfig->count, $seedConfig->params, $parentModel);

        $this->connectionDriver->insertMany($models);

        if ($seedConfig->foreach !== null) {
            foreach ($models as $model) {
                foreach ($seedConfig->foreach as $nestedSeedConfig) {
                    $this->seed($nestedSeedConfig, $model);
                }
            }
        }
    }

    private function findModelConfig(string $modelName): ModelConfig
    {
        foreach ($this->appConfig->models as $model) {
            if ($model->table === $modelName) {
                return $model;
            }
        }

        throw new \RuntimeException("Model $modelName doesn't exist. Check configuration file");
    }
}
