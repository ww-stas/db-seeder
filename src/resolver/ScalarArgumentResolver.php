<?php declare(strict_types=1);

namespace App\Resolver;

class ScalarArgumentResolver extends ArgumentResolver
{
    protected function doResolve($context = null)
    {
        return $this->method;
    }
}
