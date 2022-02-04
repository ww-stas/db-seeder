<?php declare(strict_types=1);

namespace App\Mapper;

use App\Attributes\DefaultValueResolver;
use App\ConfigValidationResult;
use App\Resolver\ArgumentResolver;
use App\ValidationException;
use App\YamlConfigurable;
use JetBrains\PhpStorm\Pure;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

class ConfigMapper
{
    #[Pure] public static function make(): static
    {
        return new static();
    }

    /**
     * @template T
     *
     * @param class-string<T> $targetClass
     *
     * @throws ReflectionException
     * @throws ValidationException
     * @return T
     *
     */
    public function mapFromFile(string $targetClass, string $configFile): YamlConfigurable
    {
        $config = Yaml::parseFile($configFile);

        return $this->map($targetClass, $config);
    }

    /**
     * @template T
     *
     * @param class-string<T> $targetClass
     *
     * @throws ValidationException
     * @throws ReflectionException
     *
     * @return T
     */
    public function map(string $targetClass, array $config): YamlConfigurable
    {
        $instance = new $targetClass;
        $classInfo = ClassInfo::make($targetClass);
        $classInfo->fixCircularReferences();
        $validationResult = $this->validate($classInfo, $config);

        if (!$validationResult->isValid()) {
            throw new ValidationException($validationResult);
        }

        return $this->doMap($classInfo, $config, $instance);
    }

    private function doMap(ClassInfo $classInfo, ?array $config, $resultInstance, $parentKey = null): YamlConfigurable
    {
        foreach ($classInfo->getFields() as $field) {
            $fieldName = $field->getName();
            //Skip values that doesn't exist in config file but has a default values
            if (($config !== null && !array_key_exists($fieldName, $config)) || $config === null) {
                //fallback to defaultValueResolver
                if (!$field->hasDefaultValueResolver()) {
                    continue;
                }

                $defaultValueResolver = $field->getDefaultValueResolver();
                switch ($defaultValueResolver) {
                    case DefaultValueResolver::PARENT_KEY:
                        $rawValue = $parentKey;
                        break;
                    case DefaultValueResolver::NESTED_LIST:
                        $rawValue = $config;
                        break;
                }
            } else {
                $rawValue = $config[$fieldName];
            }

            if (!$field->isPrimitive() || $field->isArgumentResolver()) {
                $targetClassName = $field->getType();
                if (!$field->isList()) {
                    $value = $this->doMap($field->getClassInfo(), $rawValue, $field->newInstance());
                } else {
                    $value = [];
                    foreach ($rawValue as $key => $item) {
                        if ($field->isArgumentResolver()) {
                            $value[$key] = ArgumentResolver::make($item);
                        } else {
                            $value[] = $this->doMap($field->getClassInfo(), $item, new $targetClassName, $key);
                        }
                    }
                }
            } else {
                $value = $rawValue;
            }

            $this->setValue($field, $value, $resultInstance);
        }

        return $resultInstance;
    }

    private function setValue(ClassField $field, $value, $resultInstance): void
    {
        if ($value === null && $field->hasDefaultValue()) {
            return;
        }
        if ($field->isPublic()) {
            $resultInstance->{$field->getName()} = $value;
        } else {
            $resultInstance->{$field->getSetter()}($value);
        }
    }

    private function validate(ClassInfo $classInfo, ?array $config, ?array $parent = [], ?ConfigValidationResult $validationResult = null): ConfigValidationResult
    {
        if (null === $validationResult) {
            $validationResult = new ConfigValidationResult();
        }

        $pathFunction = static fn(array $path) => implode(".", $path);

        //Check for required fields
        foreach ($classInfo->getFields() as $field) {
            $fieldName = $field->getName();
            $isFieldExistsInConfig = $config !== null && array_key_exists($fieldName, $config);
            $isRequired = $field->isRequired();
            $path = $parent;
            $path[] = $fieldName;

            if ($isRequired && !$isFieldExistsInConfig) {
                if ($field->hasDefaultValueResolver()) {
                    $defaultValueResolver = $field->getDefaultValueResolver();
                    switch ($defaultValueResolver) {
                        case DefaultValueResolver::PARENT_KEY:
                            $parentKeyExists = !empty($parent);
                            if (!$parentKeyExists) {
                                $validationResult->addError($path, sprintf("Field '%s' is required but not found in the config file", $pathFunction($path)));
                            }
                            break;
                        case DefaultValueResolver::NESTED_LIST:
                            if (empty($config) || !is_array($config)) {
                                $validationResult->addError($path, sprintf("Field '%s' is required but not found in the config file", $pathFunction($path)));
                            }
                            break;
                    }
                } else {
                    $validationResult->addError($path, sprintf("Field '%s' is required but not found in the config file", $pathFunction($path)));
                    continue;
                }
            }

            if (false === $field->isPrimitive()) {
                if ($field->isList()) {
                    if ($isRequired === false && !$isFieldExistsInConfig) {
                        continue;
                    }
                    foreach ($config[$fieldName] as $key => $value) {
//                        $path = $pathFunction($key, $path);
                        $path[] = $key;
                        $validationResult = self::validate($field->getClassInfo(), $value, $path, $validationResult);
                    }
                } else {
                    $validationResult = self::validate($field->getClassInfo(), $config[$fieldName], $path, $validationResult);
                }
            }
        }

        return $validationResult;
    }
}
