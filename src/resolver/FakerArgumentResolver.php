<?php declare(strict_types=1);

namespace App\Resolver;

use App\Context\ContextAware;
use App\Context\ContextAwareInterface;
use App\Metric;

class FakerArgumentResolver extends ArgumentResolver implements ContextAwareInterface
{
    use ContextAware;

    protected function doResolve($context = null)
    {
        $callable = [$this->appContext->getFaker(), $this->method];
        if (null === $this->argument) {
            return $callable();
        }

        if (!is_array($this->argument)) {
            $argument = [$this->argument];
        } else {
            $argument = $this->argument;
        }

        return call_user_func_array($callable, $argument);
    }
}
