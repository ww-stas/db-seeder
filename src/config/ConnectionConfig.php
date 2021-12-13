<?php declare(strict_types=1);

namespace App\Config;

use App\Attributes\Required;
use App\YamlConfigurable;

class ConnectionConfig implements YamlConfigurable
{
    #[Required]
    public string $driver;
    #[Required]
    public string $host;
    #[Required]
    public string $database;
    #[Required]
    public string $user;
    #[Required]
    public string $password;
    public ?int $port = null;
}
