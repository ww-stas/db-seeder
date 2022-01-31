<?php declare(strict_types=1);

namespace App;

use App\Config\ModelConfig;
use App\Context\Dependent;
use App\Resolver\ScalarArgumentResolver;

class TestDriver implements ConnectionDriver
{
    private $inserts = [];

    /**
     * @param Model[] $models
     */
    public function insertMany(array $models): void
    {
        $model = $models[0];
        $table = $model->getModelName();

        if (!array_key_exists($table, $this->inserts)) {
            $this->inserts[$table] = count($models);
        } else {
            $this->inserts[$table] += count($models);
        }
        Counter::getInstance()->update(count($models));
    }

    /**
     * @return array
     */
    public function getInserts(): array
    {
        return $this->inserts;
    }

    public function select(ModelConfig $model, array $condition): array
    {
        $params = [];
        foreach ($model->columns as $name => $column) {
            if ($column instanceof Dependent) {
                $params[$name] = new ScalarArgumentResolver("stub");
            }
        }

        $generator = new ModelGenerator($model);
        $result = $generator->generateMany(3, $params);
        $out = [];
        foreach ($result as $item) {
            $out[] = $item->getFields();
        }

        return $out;
    }

    public function isTableExists(string $table): bool
    {
        return array_key_exists($table, $this->inserts);
    }
}
