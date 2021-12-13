<?php declare(strict_types=1);

namespace App;

class ConfigValidationResult
{

    private array $errors = [];

    public function addError(string $path, $errorMessage): void
    {
        $this->errors[$path] = $errorMessage;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
