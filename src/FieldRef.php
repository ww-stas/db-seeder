<?php declare(strict_types=1);

namespace App;

class FieldRef
{
    private string $table;
    private string $field;

    /**
     * @param string $table
     * @param string $field
     */
    public function __construct(string $table, string $field)
    {
        $this->table = $table;
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}
