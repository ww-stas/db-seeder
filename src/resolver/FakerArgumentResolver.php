<?php declare(strict_types=1);

namespace App\Resolver;

use Faker\Factory;
use Faker\Generator;

class FakerArgumentResolver extends ArgumentResolver
{
    private Generator $faker;

    protected function doResolve($context = null)
    {
        $callable = [$this->faker, $this->method];
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

    public function getName(): string
    {
        return 'faker';
    }

    protected function init(): void
    {
        //TODO
        parent::init();
        $this->faker = Factory::create();//$this->appConfig->faker->localization);
    }
}
