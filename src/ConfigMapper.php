<?php declare(strict_types=1);

namespace App;

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
     * @throws ValidationException
     * @throws ReflectionException
     */
    public function map(string $targetClass, string $configFile): YamlConfigurable
    {
        $instance = new $targetClass;
        $config = Yaml::parseFile($configFile);

        $classInfo = ClassInfo::make($targetClass);
        $validationResult = $this->validate($classInfo, $config);

        if (!$validationResult->isValid()) {
            throw new ValidationException($validationResult);
        }

        return $instance;
    }

    private function validate(ClassInfo $classInfo, array $config, ?string $parent = null): ConfigValidationResult
    {
        $result = new ConfigValidationResult();

        $pathFunction = fn($key, ?string $parent = null) => null === $parent ? $key : "$parent.$key";

        //Check for required fields
        foreach ($classInfo->getFields() as $field) {
            $fieldName = $field->getName();
            $isFieldExistsInConfig = array_key_exists($fieldName, $config);
            $isRequired = $field->isRequired();

            if ($isRequired && !$isFieldExistsInConfig) {
                $path = $pathFunction($fieldName, $parent);
                $result->addError($path, sprintf("Field '%s' is required but not found in the config file", $path));
            }
        }

        //Check for unknown fields
        foreach ($config as $key => $value) {
            $field = $classInfo->getClassField($key);
            $path = $pathFunction($key, $parent);
            if (null === $field) {
                $result->addError($path, sprintf("Unknown config path '%s'", $path));
            }
        }

        return $result;
    }


}
