<?php declare(strict_types=1);

namespace App;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Config
{
    private string $configFile;
    private ?array $config = null;

    /**
     * @param string $configFile
     */
    public function __construct(string $configFile)
    {
        $this->configFile = $configFile;
    }

    public function getConfig(string $path, string $class): mixed
    {
        $config = $this->loadConfig();

        if (false === array_key_exists($path, $config)) {
            throw new RuntimeException(printf('The config path [%s] doesn\'t exist', $path));
        }

        return Mapper::map($config[$path], $class);
    }

    public function loadConfig(): array
    {
        if ($this->config === null) {
            $this->config = Yaml::parseFile($this->configFile);
        }

        return $this->config;
    }
}
