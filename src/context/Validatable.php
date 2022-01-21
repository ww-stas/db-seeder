<?php declare(strict_types=1);

namespace App\Context;

use App\ValidationException;

interface Validatable extends ContextAwareInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(): void;
}
