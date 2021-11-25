<?php declare(strict_types=1);

namespace App\Config;

use App\Attributes\Collection;
use App\Attributes\Constructor;
use App\Attributes\Required;
use App\Resolver\ArgumentResolver;
use App\YamlConfigurable;

class ModelConfig implements YamlConfigurable
{
    #[Required]
    public string $table;
    /**
     * @var ArgumentResolver[]
     */
    #[Required]
    #[Collection(class: ArgumentResolver::class)]
    #[Constructor(value: Constructor::STATIC_MAKE)]
    public array $columns;
}
