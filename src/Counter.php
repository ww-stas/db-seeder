<?php declare(strict_types=1);

namespace App;

class Counter
{
    private static ?Counter $instance = null;
    private int $total;
    private int $current = 0;


    private function __construct()
    {
    }

    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * @param int $total
     *
     * @return Counter
     */
    public function setTotal(int $total): Counter
    {
        $this->total = $total;

        return $this;
    }

    public function update(int $count)
    {
        $this->current += $count;
        $percent = (int)(($this->current / $this->total) * 100);
        echo "\r $this->current / $this->total ($percent%)";
    }
}
