<?php declare(strict_types=1);

namespace App;

class Model
{
    private string $modelName;
    private array $fields;

    /**
     * @param string $modelName
     * @param array  $fields
     */
    public function __construct(string $modelName, array $fields)
    {
        $this->modelName = $modelName;
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
