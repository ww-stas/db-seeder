<?php declare(strict_types=1);

namespace App\Resolver\Variables;

abstract class CustomVariable
{
    abstract public function getName(): string;
    abstract public function resolve(string $argument);
}
