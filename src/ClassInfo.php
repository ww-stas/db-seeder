<?php declare(strict_types=1);

namespace App;

use App\Attributes\Collection;
use App\Attributes\Required;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class ClassInfo
{
    /**
     * @var ClassField[]
     */
    private array $fields = [];

    /**
     * @throws ReflectionException
     */
    public static function make(string $targetClass)
    {
        $instance = new static();

        $reflection = new ReflectionClass($targetClass);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $classField = new ClassField();
            $propertyName = $property->getName();

            $classField->setName($propertyName);
            $classField->setRequired(self::isRequired($property));
            self::resolveType($property, $classField);
            self::resolveSetter($reflection, $property, $classField);

            $instance->addClassField($propertyName, $classField);
        }

        return $instance;
    }

    private static function resolveSetter(ReflectionClass $reflectionClass, ReflectionProperty $reflectionProperty, ClassField $classField): void
    {
        $isPublic = $reflectionProperty->isPublic();
        $classField->setIsPublic($isPublic);

        if (true === $isPublic) {
            return;
        }

        $possibleSetter = "set" . ucfirst($reflectionProperty->getName());
        if (false === $reflectionClass->hasMethod($possibleSetter)) {
            throw new \RuntimeException(sprintf(
                "Unable to find suitable setter for property %s for class %s",
                $reflectionProperty->getName(),
                $reflectionClass->getName()
            ));
        }

        $classField->setSetter($possibleSetter);
    }

    /**
     * @throws ReflectionException
     */
    private static function resolveType(ReflectionProperty $reflectionProperty, ClassField $classField): void
    {
        $type = $reflectionProperty->getType();
        $typeName = $type->getName();
        $isNested = false;
        $isList = false;
        if (!$type->isBuiltin()) {
            $isNested = is_subclass_of($type->getName(), YamlConfigurable::class);
        } else if ('array' === $type->getName()) {
            $attributes = $reflectionProperty->getAttributes(Collection::class);
            if (!empty($attributes)) {
                $isNested = true;
                $isList = true;
                /** @var Collection $attribute */
                $attribute = $attributes[0]->newInstance();
                $typeName = $attribute->getClass();
            }
        }

        $classField->setType($typeName);
        $classField->setIsList($isList);
        if ($isNested && is_subclass_of($typeName, YamlConfigurable::class)) {
            $classField->setClassInfo(static::make($typeName));
        }
    }

    /**
     * There are should be 3 ways how to figure out whether the field is required or not.
     * 1. Use attribute #Required. The most preferable way
     * 2. Use property typehint e.g
     * ```
     * private int $value
     * ```
     * would be treated as required and
     * ```
     * private ?int $value
     * ```
     * would br treated as optional(non required) field
     * 3. phpDoc comment. If doc comment contains `@var` type and the type contains `null|...` or `...|null' that would
     * be treated as optional, otherwise as required.
     *
     * If all three ways doesn't give a result the field would be treated as optional.
     *
     * @return bool
     */
    private static function isRequired(ReflectionProperty $reflectionProperty): bool
    {
        $attributes = $reflectionProperty->getAttributes(Required::class);
        if (!empty($attributes)) {
            return true;
        }

        $propertyType = $reflectionProperty->getType();
        if (null !== $propertyType) {
            return !$propertyType->allowsNull();
        }

        $doc = $reflectionProperty->getDocComment();
        if (false === $doc) {
            return false;
        }

        if (preg_match('/@var\s+(\S+)/', $doc, $matches)) {
            [, $type] = $matches;

            return str_contains($type, 'null|') or str_contains($type, '|null');
        }

        return false;
    }

    /**
     * @return ClassField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getClassField(string $fieldName): ?ClassField
    {
        return $this->fields[$fieldName] ?? null;
    }

    public function addClassField(string $property, ClassField $classField): void
    {
        $this->fields[$property] = $classField;
    }
}
