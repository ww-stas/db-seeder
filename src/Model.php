<?php declare(strict_types=1);

namespace App;

class Model
{
    private string $modelName;
    private array $fields;
    private ?Model $parent = null;

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

    /**
     * @return Model|null
     */
    public function getParent(): ?Model
    {
        return $this->parent;
    }

    /**
     * @param Model|null $parent
     *
     * @return Model
     */
    public function setParent(?Model $parent): Model
    {
        $this->parent = $parent;

        return $this;
    }

    public function __toString(): string
    {
        $output = $this->modelName . '(';
        foreach ($this->fields as $field => $value) {
            $output .= "$field='$value', ";
        }

        return rtrim($output, ", ") . ");";
    }
}
