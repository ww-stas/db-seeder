<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class Metric
{
    private static array $metrics = [];

    public static function start(string $name): void
    {
        self::$metrics[$name][] = new MetricItem();
    }

    public static function stop(string $name): void
    {
        if (!array_key_exists($name, self::$metrics)) {
            return;
        }

        $items = array_filter(self::$metrics[$name], static fn(MetricItem $item) => $item->getStop() === null);
        if (empty($items)) {
            return;
        }
        array_values($items)[0]->stop();
    }

    public static function summary(): array
    {
        echo "\n";
        $result = [];
        $precision = 2;
        /**
         * @var string       $name
         * @var MetricItem[] $metric
         */
        foreach (self::$metrics as $name => $metric) {
            $sum = 0;

            foreach ($metric as $value) {
                $sum += $value->diff();
            }
            $sum = round($sum, $precision);
            $count = count($metric);
            $avg = round($sum / count($metric), $precision);

            $result[$name] = [
                'name'  => $name,
                'avg'   => $avg ." ms",
                'sum'   => $sum ." ms",
                'count' => $count,
            ];
        }

        return $result;
    }

    public static function print(OutputInterface $output): void
    {
        $summary = self::summary();
        usort($summary, fn($a, $b) => $b['count'] <=> $a['count']);
        $table = new Table($output);
        $table
            ->setHeaders(['Task', 'Avg. execution time', 'Summary execution time ', 'Count of executions'])
            ->setRows($summary);
        $table->render();
    }
}
