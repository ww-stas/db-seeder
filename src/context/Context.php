<?php declare(strict_types=1);

namespace App\Context;

use App\Cache;
use App\Config\AppConfig;
use App\ConnectionDriver;
use App\Resolver\ArgumentResolver;
use Faker\Factory;
use Faker\Generator;

class Context
{
    private AppConfig $config;
    private ConnectionDriver $connection;
    private Generator $faker;
    private Cache $cache;

    public function __construct(AppConfig $config, ConnectionDriver $connection, Cache $cache)
    {
        $this->config = $config;
        $this->connection = $connection;
        $this->cache = $cache;
        $this->createFaker();
    }

    /**
     * @return Cache
     */
    public function getCache(): Cache
    {
        return $this->cache;
    }

    public function scan(): void
    {
        foreach ($this->config->models as $model) {
            foreach ($model->columns as $resolver) {
                if ($resolver instanceof ContextAwareInterface) {
                    $resolver->setAppContext($this);
                }
                $this->validate($resolver);
            }
        }
    }

    /**
     * @return AppConfig
     */
    public function getConfig(): AppConfig
    {
        return $this->config;
    }

    /**
     * @return ConnectionDriver
     */
    public function getConnection(): ConnectionDriver
    {
        return $this->connection;
    }

    /**
     * @return Generator
     */
    public function getFaker(): Generator
    {
        return $this->faker;
    }

    private function validate(ArgumentResolver $resolver): void
    {
        if ($resolver instanceof Validatable) {
            $resolver->validate();
        }
    }

    private function createFaker(): void
    {
        $this->faker = Factory::create($this->config->faker->localization);
    }
}
