<?php declare(strict_types=1);

namespace App\Resolver;

use App\Model;
use RuntimeException;

class RefArgumentResolver extends ArgumentResolver
{
    /**
     * @param Model $context
     *
     * @return string
     */
    public function resolve($context = null)
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
