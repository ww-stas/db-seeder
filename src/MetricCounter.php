<?php declare(strict_types=1);

namespace App;

use JetBrains\PhpStorm\Pure;

class MetricCounter
{
    private const PRECISION = 2;
    private string $name;
    private float $avgTime = 0.0;
    private float $totalTime = 0.0;
    private int $count = 0;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    #[Pure] public function getAvgTime(): float
    {
        return round($this->totalTime / $this->getCount(), self::PRECISION);
    }

    /**
     * @return float
     */
    public function getTotalTime(): float
    {
        return round($this->totalTime, self::PRECISION);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    public function addItem(MetricItem $metricItem): void
    {
        $this->count++;
        $diff = $metricItem->diff();
        $this->totalTime += $diff;
    }
}
