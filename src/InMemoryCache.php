<?php declare(strict_types=1);

namespace App;

class InMemoryCache implements Cache
{
    private array $values = [];

    public function isset(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function get(string $key): mixed
    {
        if (!$this->isset($key)) {
            throw new \RuntimeException("Cache doesn't exist");
        }

        return $this->values[$key];
    }

    public function set(string $key, $value): void
    {
        $this->values[$key] = $value;
    }

}
