<?php declare(strict_types=1);

namespace App;

use App\Config\ModelConfig;

class ModelGenerator
{
    private ModelConfig $modelConfig;

    /**
     * @param ModelConfig $modelConfig
     */
    public function __construct(ModelConfig $modelConfig)
    {
        $this->modelConfig = $modelConfig;
    }

    public function generate(array $params = [], ?Model $parentModel = null): Model
    {
        $fields = [];
        foreach ($this->modelConfig->columns as $name => $column) {
            $fields[$name] = $column->resolve($parentModel);
        }

        if ($parentModel !== null && !empty($params)) {
            foreach ($params as $name => $param) {
                $fields[$name] = $param->resolve($parentModel);
            }
        }

        $model = new Model($this->modelConfig->table, $fields);
        if ($parentModel !== null) {
            $model->setParent($parentModel);
        }

        return $model;
    }

    /**
     * @param int        $count
     * @param array      $params
     * @param Model|null $parenModel
     *
     * @return Model[]
     */
    public function generateMany(int $count, array $params = [], ?Model $parenModel = null): array
    {
        $models = [];
        for ($i = 0; $i < $count; $i++) {
            $models[] = $this->generate($params, $parenModel);
        }

        return $models;
    }
}
