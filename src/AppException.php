<?php declare(strict_types=1);

namespace App;

use JetBrains\PhpStorm\Pure;

class AppException extends \Exception
{
    #[Pure]
    public function getErrorMessage(): string
    {
        return $this->getMessage();
    }
}
