<?php declare(strict_types=1);

namespace App\Resolver;

use App\Context\Dependent;
use App\Model;
use RuntimeException;

class RefArgumentResolver extends ArgumentResolver implements Dependent
{
    public function getTables(): array
    {
        [$tableName,] = explode(".", $this->method);

        return [$tableName];
    }

    public function getFieldName(): string
    {
        [, $fieldName] = explode(".", $this->method);

        return $fieldName;
    }

    /**
     * @param Model $context
     *
     * @return string
     */
    protected function doResolve($context = null)
    {
        [$modelName, $fieldName] = explode(".", $this->method);
        if (!$context instanceof Model) {
            throw new RuntimeException("The given context should be of type Model");
        }

        if ($context->getModelName() !== $modelName) {
            throw new RuntimeException("Referenced model doesn't match to given context");
        }

        if (!array_key_exists($fieldName, $context->getFields())) {
            throw new RuntimeException("Referenced model doesn't match to given context");
        }

        return $context->getFields()[$fieldName];
    }
}
