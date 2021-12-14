<?php declare(strict_types=1);

namespace App;

use App\Config\AppConfig;
use App\Config\ModelConfig;
use App\Config\SeedConfig;

class Seeder
{
    private AppConfig $appConfig;
    private ConnectionDriver $connectionDriver;

    /**
     * @param AppConfig $appConfig
     */
    public function __construct(AppConfig $appConfig)
    {
        $this->appConfig = $appConfig;
        $connection = Connection::make($appConfig->getConnectionConfig());
        $this->connectionDriver = new DoctrineDriver($connection);
    }

    public function run(): void
    {
        foreach ($this->appConfig->seed as $seedConfig) {
            $this->seed($seedConfig);
        }
    }

    private function seed(SeedConfig $seedConfig, Model $parentModel = null): void
    {
        $modelConfig = $this->findModelConfig($seedConfig->model);

        /** @var Model[] $models */
        $models = [];
        for ($i = 0; $i < $seedConfig->count; $i++) {
            $model = $this->generateModel($modelConfig, $seedConfig, $parentModel);
            if ($parentModel !== null) {
                $model->setParent($parentModel);
            }
            $models[] = $model;
        }

        $this->connectionDriver->insertMany($modelConfig->table, $models);
        if ($seedConfig->foreach !== null) {
            foreach ($models as $model) {
                foreach ($seedConfig->foreach as $nestedSeedConfig) {
                    $this->seed($nestedSeedConfig, $model);
                }
            }
        }
    }

    private function generateModel(ModelConfig $modelConfig, SeedConfig $seedConfig, Model $parentModel = null): Model
    {
        $fields = [];
        foreach ($modelConfig->columns as $name => $column) {
            $fields[$name] = $column->resolve($parentModel);
        }

        if ($parentModel !== null && !empty($seedConfig->params)) {
            foreach ($seedConfig->params as $name => $param) {
                $fields[$name] = $param->resolve($parentModel);
            }
        }

        return new Model($modelConfig->table, $fields);
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
