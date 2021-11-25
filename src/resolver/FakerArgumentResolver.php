<?php declare(strict_types=1);

namespace App\Resolver;

use App\Attributes\Component;
use Faker\Factory;
use Faker\Generator;

#[Component]
class FakerArgumentResolver extends ArgumentResolver
{
    private Generator $faker;

    public function resolve()
    {
        $callable = [$this->faker, $this->method];
        if (null === $this->argument) {
            return $callable();
        }

        return $callable($this->argument);
    }

    public function getName(): string
    {
        return 'faker';
    }

    protected function init(): void
    {
        parent::init();
        $this->faker = Factory::create();//$this->appConfig->faker->localization);
    }
}
