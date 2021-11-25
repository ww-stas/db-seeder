<?php declare(strict_types=1);

namespace App\Resolver;

use App\Config\AppConfig;

abstract class ArgumentResolver
{
    protected string $method;
    protected $argument;
    protected AppConfig $appConfig;

    /**
     * @param string $method
     * @param string $argument
     */
    final public function __construct(string $method, $argument = null)
    {
        $this->method = $method;
        $this->argument = $argument;
    }

    public static function make(string $value): static
    {
        if (!preg_match('/\$?\w+::\w+(::\w+)?/', $value)) {
            return new ScalarArgumentResolver($value);
        }

        $result = explode("::", $value);
        if (count($result) === 3) {
            [$provider, $method, $argument] = $result;
        } else {
            [$provider, $method] = $result;
            $argument = null;
        }

        if (preg_match('/\$([a-z]+)/i', $provider, $matches)) {
            $provider = $matches[1];
        }
        if (null !== $argument && preg_match('/\[((.+),?)+]/', $argument, $matches)) {
            $argument = array_map(fn($item) => trim($item), explode(',', $matches[1]));
        }

        $providerClass = 'App\\Resolver\\' . ucfirst($provider) . "ArgumentResolver";
        if (!class_exists($providerClass)) {
            throw new \RuntimeException("Provider $providerClass doesn't exist");
        }
        if (!is_subclass_of($providerClass, __CLASS__)) {
            throw new \RuntimeException("The provider $providerClass must extends Provider abstract class");
        }

        /** @var  ArgumentResolver $instance */
        $instance = new $providerClass($method, $argument);
        $instance->init();

        return $instance;
    }

    /**
     * @param AppConfig $appConfig
     *
     * @return ArgumentResolver
     */
    public function setAppConfig(AppConfig $appConfig): ArgumentResolver
    {
        $this->appConfig = $appConfig;

        return $this;
    }

    abstract public function resolve();

    protected function init(): void
    {
    }
}
