<?php declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Collection
{
    private string $class;

    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
