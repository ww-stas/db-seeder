<?php declare(strict_types=1);

namespace App;

class MetricItem
{
    private float $start;
    private ?float $stop;

    public function __construct()
    {
        $this->start = microtime(true);
        $this->stop = null;
    }

    /**
     * @return float
     */
    public function getStart(): float
    {
        return $this->start;
    }

    public function getStop(): ?float
    {
        return $this->stop;
    }

    public function stop(): void
    {
        $this->stop = microtime(true);
    }

    public function diff(): float
    {
        return ($this->stop - $this->start) * 1000;
    }
}
