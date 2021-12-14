<?php declare(strict_types=1);

namespace App\Resolver;

class SubstringArgumentResolver extends ArgumentResolver
{
    protected function doResolve($context = null): string
    {
        return substr($context, (int)$this->method);
    }
}
