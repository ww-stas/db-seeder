<?php declare(strict_types=1);

namespace App\Resolver;

class ScalarArgumentResolver extends ArgumentResolver
{
    public function resolve($context = null)
    {
        return $this->method;
    }
}
