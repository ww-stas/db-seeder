<?php declare(strict_types=1);

namespace App\Resolver\Variables;

use DateTime;

/**
 * $var::now::Y-m-d H:i:s
 */
class NowCustomVar extends CustomVariable
{
    public function getName(): string
    {
        return 'now';
    }

    public function resolve(string $argument)
    {
        return (new DateTime())->format($argument);
    }
}
