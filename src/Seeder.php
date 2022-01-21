<?php declare(strict_types=1);

namespace App;

use App\Config\AppConfig;
use App\Config\ModelConfig;
use App\Config\SeedConfig;

class Seeder
{
    private AppConfig $appConfig;
    private ConnectionDriver $connectionDriver;


    public function __construct(AppConfig $appConfig, ConnectionDriver $connectionDriver)
    {
        $this->appConfig = $appConfig;
        $this->connectionDriver = $connectionDriver;
    }

    public function run(): void
    {
        //TODO continue
        //$plan = $this->createPlan();
        $count = 0;
        foreach ($this->appConfig->seed as $seedConfig) {
            $count += $this->calc($seedConfig);
        }
        Counter::getInstance()->setTotal($count);

        echo "Going to insert $count rows\n";

        foreach ($this->appConfig->seed as $seedConfig) {
            $this->wrapSeed($seedConfig);
        }
    }

    private function createPlan(): array
    {
        //Order
        $plan = [];
        foreach ($this->appConfig->models as $modelConfig) {
            $plan[] = [
                'table'  => $modelConfig->table,
                'depsOn' => $modelConfig->getDepsOn(),
            ];
        }

        for ($i = 0, $count = count($plan) - 1; $i < $count; $i++) {
            $item = $plan[$i];
            $table = $item['table'];

            $next = $plan[$i + 1];
            $nextDeps = $next['depsOn'];
            for ($j = 0, $countDeps = count($nextDeps); $j < $countDeps; $j++) {
                $depItem = $nextDeps[$j];
                if ($table === $depItem) {
                    $plan[$i + 1]['depsOn'][$j] = $item;
                    unset($plan[$i]);
                }
            }

        }

        return $plan;
    }

    private function calc(SeedConfig $seedConfig): int
    {
        $count = $seedConfig->count;

        if ($seedConfig->foreach === null) {
            return $count;
        }

        $subcount = $count;
        foreach ($seedConfig->foreach as $nested) {
            $count += $subcount * $this->calc($nested);
        }

        return $count;
    }

    private function wrapSeed(SeedConfig $seedConfig, Model $parentModel = null): void
    {
        $count = $seedConfig->count;
        $maxCountOfModels = 100;

        $t = intdiv($count, $maxCountOfModels);
        $v = $count % $maxCountOfModels;

        for ($i = 0; $i < $t; $i++) {
            $copySeedConfig = clone $seedConfig;
            $copySeedConfig->count = $maxCountOfModels;
            $this->seed($copySeedConfig, $parentModel);
        }

        if ($v !== 0) {
            $copySeedConfig = clone $seedConfig;
            $copySeedConfig->count = $v;
            $this->seed($copySeedConfig, $parentModel);
        }
    }

    private function seed(SeedConfig $seedConfig, Model $parentModel = null): void
    {
        $modelConfig = $this->findModelConfig($seedConfig->model);

        $generator = new ModelGenerator($modelConfig);
        $models = $generator->generateMany($seedConfig->count, $seedConfig->params, $parentModel);

        $this->connectionDriver->insertMany($models);

        if ($seedConfig->foreach !== null) {
            foreach ($models as $model) {
                foreach ($seedConfig->foreach as $nestedSeedConfig) {
                    $this->wrapSeed($nestedSeedConfig, $model);
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
