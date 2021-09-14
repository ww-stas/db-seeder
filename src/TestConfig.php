<?php declare(strict_types=1);

namespace App;

use Symfony\Contracts\Service\Attribute\Required;

class TestConfig
{
    #[Required]
    private int $first;

    private ?int $second;

    private int $third;

    /**
     * @var null|string
     */
    private $fourth;

    private $fivth;
}
