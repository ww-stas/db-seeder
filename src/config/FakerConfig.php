<?php declare(strict_types=1);

namespace App\Config;

use App\YamlConfigurable;

class FakerConfig implements YamlConfigurable
{
    public string $localization = 'en_US';
}
