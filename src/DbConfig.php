<?php declare(strict_types=1);

namespace App;

class DbConfig
{
    public string $host;
    public string $database;
    public string $user;
    public string $password;
    public ?string $port = '3306';
}