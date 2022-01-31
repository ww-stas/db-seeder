<?php declare(strict_types=1);

namespace App;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class Metric
{
    /**
     * @var MetricCounter[]
     */
    private static array $metrics = [];
    /**
     * @var MetricItem[]
     */
    private static array $tmp = [];

    public static function start(string $name): void
    {
//        return;
        self::$tmp[$name] = new MetricItem();
    }

    public static function stop(string $name): void
    {
//        return;
        if (!array_key_exists($name, self::$tmp)) {
            return;
        }

        $item = self::$tmp[$name];
        $item->stop();
        if (array_key_exists($name, self::$metrics)) {
            $counter = self::$metrics[$name];
        } else {
            $counter = new MetricCounter($name);
            self::$metrics[$name] = $counter;
        }

        $counter->addItem($item);
    }


    #[Pure]
    public static function summary(): array
    {
        $result = [];
        $precision = 2;
        /**
         * @var string       $name
         * @var MetricItem[] $metric
         */
        foreach (self::$metrics as $name => $metric) {
//            $sum = 0;
//
//            foreach ($metric as $value) {
//                $sum += $value->diff();
//            }
//            $sum = round($sum, $precision);
//            $count = count($metric);
//            $avg = round($sum / count($metric), $precision);

            $result[$name] = [
                'name'  => $metric->getName(),
                'avg'   => $metric->getAvgTime() . " ms",
                'sum'   => $metric->getTotalTime() . " ms",
                'count' => $metric->getCount(),
            ];
        }

        return $result;
    }

    public static function print(OutputInterface $output): void
    {
        $summary = self::summary();
        usort($summary, static fn($a, $b) => $b['count'] <=> $a['count']);
        Console::message("");
        $table = new Table($output);
        $table
            ->setHeaders(['Task', 'Avg. execution time', 'Summary execution time ', 'Count of executions'])
            ->setRows($summary);
        $table->render();
    }
}
