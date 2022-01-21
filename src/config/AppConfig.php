<?php declare(strict_types=1);

namespace App\Config;

use App\Attributes\Collection;
use App\Attributes\Required;
use App\YamlConfigurable;

class AppConfig implements YamlConfigurable
{
    public FakerConfig $faker;
    /**
     * @var ModelConfig[]
     */
    #[Required]
    #[Collection(ModelConfig::class)]
    public array $models;
    /**
     * @var SeedConfig[]
     */
    #[Required]
    #[Collection(SeedConfig::class)]
    public array $seed;

    #[Required]
    private ConnectionConfig $connection;

    public function __construct()
    {
        $this->faker = new FakerConfig();
    }

    /**
     * @param ConnectionConfig $connection
     *
     * @return AppConfig
     */
    public function setConnection(ConnectionConfig $connection): AppConfig
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return ConnectionConfig
     */
    public function getConnectionConfig(): ConnectionConfig
    {
        return $this->connection;
    }

    /**
     * Checks whether the given model name exists in the config.
     *
     * @param string $modelName
     *
     * @return bool
     */
    public function modelExist(string $modelName): bool
    {
        $modelNames = array_map(static fn(ModelConfig $model) => $model->table, $this->models);

        return in_array($modelName, $modelNames, true);
    }

    public function findModelConfig(string $modelName): ?ModelConfig {
        foreach ($this->models as $modelConfig) {
            if ($modelConfig->table === $modelName) {
                return $modelConfig;
            }
        }

        return null;
    }
}
