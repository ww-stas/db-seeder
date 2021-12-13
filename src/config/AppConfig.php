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


}
