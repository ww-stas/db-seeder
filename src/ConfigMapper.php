<?php declare(strict_types=1);

namespace App;

use App\Resolver\ArgumentResolver;
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
     * @throws ValidationException
     * @throws ReflectionException
     *
     * @return T
     */
    public function map(string $targetClass, string $configFile): YamlConfigurable
    {
        $instance = new $targetClass;
        $config = Yaml::parseFile($configFile);

        $classInfo = ClassInfo::make($targetClass);
        $classInfo->fixCircularReferences();
        $validationResult = $this->validate($classInfo, $config);

        if (!$validationResult->isValid()) {
            throw new ValidationException($validationResult);
        }

        return $this->doMap($classInfo, $config, $instance);
    }

    private function doMap(ClassInfo $classInfo, array $config, $resultInstance): YamlConfigurable
    {
        foreach ($classInfo->getFields() as $field) {
            $fieldName = $field->getName();
            //Skip values that doesn't exist in config file but has a default values
            if (!array_key_exists($fieldName, $config)) {
                continue;
            }
            $rawValue = $config[$fieldName];
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
                            $value[] = $this->doMap($field->getClassInfo(), $item, new $targetClassName);
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

    private function setValue(ClassField $field, $value, $resultInstance)
    {
        $isArgumentResolver = $field->getType() === ArgumentResolver::class;
        if ($value === null && $field->hasDefaultValue()) {
            return;
        }
        if ($field->isPublic()) {
            $resultInstance->{$field->getName()} = $value;
        } else {
            $resultInstance->{$field->getSetter()}($value);
        }
    }


    private function validate(ClassInfo $classInfo, array $config, ?string $parent = null, ?ConfigValidationResult $validationResult = null): ConfigValidationResult
    {
        if (null === $validationResult) {
            $validationResult = new ConfigValidationResult();
        }

        $pathFunction = fn($key, ?string $parent = null) => null === $parent ? $key : "$parent.$key";

        //Check for required fields
        foreach ($classInfo->getFields() as $field) {
            $fieldName = $field->getName();
            $isFieldExistsInConfig = array_key_exists($fieldName, $config);
            $isRequired = $field->isRequired();
            $path = $pathFunction($fieldName, $parent);

            if ($isRequired && !$isFieldExistsInConfig) {
                $validationResult->addError($path, sprintf("Field '%s' is required but not found in the config file", $path));
                continue;
            }

            if (false === $field->isPrimitive()) {
                if ($field->isList()) {
                    if ($isRequired === false && !$isFieldExistsInConfig) {
                        continue;
                    }
                    foreach ($config[$fieldName] as $key => $value) {
                        $path = $pathFunction($key, $path);
                        $validationResult = self::validate($field->getClassInfo(), $value, $path, $validationResult);
                    }
                } else {
                    $validationResult = self::validate($field->getClassInfo(), $config[$fieldName], $path, $validationResult);
                }
            }
        }

        //Check for unknown fields
//        foreach ($config as $key => $value) {
//            $field = $classInfo->getClassField($key);
//            $path = $pathFunction($key, $parent);
//            if (null === $field) {
//                $validationResult->addError($path, sprintf("Unknown config path '%s'", $path));
//            }
//        }

        return $validationResult;
    }


}
