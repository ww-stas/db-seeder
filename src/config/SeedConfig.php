<?php declare(strict_types=1);

namespace App\Config;

use App\Attributes\Collection;
use App\Attributes\Constructor;
use App\Attributes\DefaultValueResolver;
use App\Attributes\Required;
use App\Resolver\ArgumentResolver;
use App\YamlConfigurable;

class SeedConfig implements YamlConfigurable
{
    public const ACTION_GENERATE = 'action.generate';

    /**
     * The action should be applied to this config.
     * Default value is generate $count models.
     *
     * @var string
     */
    public string $action = self::ACTION_GENERATE;

    /**
     * The model name. Should be one of defined in ModelsConfig
     *
     * @var string
     */
    #[Required]
    #[DefaultValueResolver(resolver: DefaultValueResolver::PARENT_KEY)]
    public string $model;

    /**
     * Count of entities that should be created
     *
     * @var int
     */
    public int $count = 1;

    /**
     * @var SeedConfig[]|null
     */
    #[Collection(SeedConfig::class)]
    public ?array $foreach = null;

    /**
     * @var ArgumentResolver[]
     */
    #[Collection(class: ArgumentResolver::class)]
    #[Constructor(value: Constructor::STATIC_MAKE)]
    public array $params = [];

}
