<?php declare(strict_types=1);

namespace App;

class DependencyCollection
{
    /**
     * @var Dependency[]
     */
    private array $dependencies = [];

    public function add(Dependency $dependency)
    {
        $this->dependencies[] = $dependency;
    }

    public function createAndAdd(string $field, string $depTable, string $depField)
    {
        $dependency = new Dependency($field, $depTable, $depField);
        $this->add($dependency);
    }

    public function findByTableName(string $tableName): ?Dependency
    {
        foreach ($this->dependencies as $dependency) {
            if ($dependency->getDepTable() === $tableName) {
                return $dependency;
            }
        }

        return null;
    }


}
