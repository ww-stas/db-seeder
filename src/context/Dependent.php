<?php declare(strict_types=1);

namespace App\Context;

/**
 * Shows that argument resolver depends on parent or another table
 */
interface Dependent
{
    public function getTables(): array;

    public function getFieldName(): string;
}
