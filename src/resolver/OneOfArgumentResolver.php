<?php declare(strict_types=1);

namespace App\Resolver;

use App\Context\ContextAware;
use App\Context\ContextAwareInterface;
use App\Context\Dependent;
use App\Context\Validatable;
use App\Model;
use App\ModelConfigValidationException;


/**
 * Resolver for the polymorphic relations where is needed
 * to select random model
 */
class OneOfArgumentResolver extends ArgumentResolver implements ContextAwareInterface, Validatable, Dependent
{
    use ContextAware;

    /**
     * @throws ModelConfigValidationException
     */
    public function validate(): void
    {
        $tableName = $this->getTableName();

        $tableExist = $this->appContext->getConnection()->isTableExists($tableName);

        $modelExists = $this->appContext->getConfig()->modelExist($tableName);
        if ($tableExist === false && $modelExists === false) {
            throw new ModelConfigValidationException("Configuration contains oneOf argument resolver which points to `$tableName` table which isn't defined in the config and doesn't exist in the database");
        }
    }

    public function getTables(): array
    {
        if (is_array($this->method)) {
            return $this->method;
        }

        return [$this->method];
    }

    public function getFieldName(): string
    {
        return $this->argument;
    }

    /**
     * @param ?Model $context
     *
     * @return mixed
     */
    protected function doResolve($context = null): mixed
    {
        $table = $this->getTableName();
        $condition = null;
        if ($context !== null && $context->getParent() !== null) {
            $parentModel = $context->getModelName();
            $thisModel = $this->appContext->getConfig()->findModelConfig($table);
            if ($thisModel !== null) {
                $depsOn = $thisModel->getDepsOn();
                $dep = $depsOn->findByTableName($parentModel);
                if ($dep !== null) {
                    $value = $context->getFields()[$dep->getDepField()];
                    $condition = [$dep->getField(), '=', $value];
                }
            }
        }

        $fieldName = $this->argument;
        $cache = $this->appContext->getCache();
        $modelConfig = $this->appContext->getConfig()->findModelConfig($table);
        $key = $table;
        if ($condition !== null) {
            $key = $table . implode(".", $condition);
        }
        if ($cache->isset($key)) {
            $rows = $cache->get($key);
        } else {
            $rows = $this->appContext->getConnection()->select($modelConfig, $condition);
            $cache->set($key, $rows);
        }

        $row = $this->appContext->getFaker()->randomElement($rows);

        if (!array_key_exists($fieldName, $row)) {
            throw new \RuntimeException("The `oneOf` resolver for the table `$table` returned the empty row or the row doesn't contain `$fieldName`");
        }

        return $row[$this->argument];
    }

    private function getTableName(): string
    {
        $table = $this->method;
        if (!is_array($table)) {
            return $table;
        }

        return $this->appContext->getFaker()->randomElement($table);
    }
}
