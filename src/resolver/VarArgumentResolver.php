<?php declare(strict_types=1);

namespace App\Resolver;

class VarArgumentResolver extends ArgumentResolver
{
    public function resolve($context = null)
    {
        return VarArgumentResolverConfig::make()->findAndResolve($this->method, $this->argument);
    }
}
