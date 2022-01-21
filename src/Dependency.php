<?php declare(strict_types=1);

namespace App;

class Dependency
{
    /**
     * The name of field which connected to dependent table;
     *
     * @var string
     */
    private string $field;
    /**
     * The name of the table from which the current table depends.
     *
     * @var string
     */
    private string $depTable;
    private string $depField;

    /**
     * @param string $field
     * @param string $depTable
     * @param string $depField
     */
    public function __construct(string $field, string $depTable, string $depField)
    {
        $this->field = $field;
        $this->depTable = $depTable;
        $this->depField = $depField;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDepTable(): string
    {
        return $this->depTable;
    }

    /**
     * @return string
     */
    public function getDepField(): string
    {
        return $this->depField;
    }
}
