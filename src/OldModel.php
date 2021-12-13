<?php declare(strict_types=1);

namespace App;

use Faker\Generator;

abstract class OldModel
{
    abstract public function value(Generator $faker): array;

    abstract public function tableName(): string;
}
