<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class Counter
{
    private static ?Counter $instance = null;
    private ProgressBar $progressBar;
    private OutputInterface $output;

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
     * @param OutputInterface $output
     *
     * @return Counter
     */
    public function setOutput(OutputInterface $output): Counter
    {
        $this->output = $output;

        return $this;
    }


    /**
     * @param int $total
     *
     * @return Counter
     */
    public function setTotal(int $total): Counter
    {
        $this->progressBar = new ProgressBar($this->output, $total);
        $this->progressBar->start();

        return $this;
    }

    public function update(int $count): void
    {
        $this->progressBar->advance($count);
    }
}
