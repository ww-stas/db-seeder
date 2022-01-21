<?php declare(strict_types=1);

namespace App;

interface Cache
{
    public function isset(string $key): bool;

    public function get(string $key): mixed;

    public function set(string $key, $value): void;
}
