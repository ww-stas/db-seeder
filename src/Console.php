<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Console\Output\OutputInterface;

class Console
{
    private static OutputInterface $output;

    public static function init(OutputInterface $output): void
    {
        self::$output = $output;
    }

    public static function message(string $message): void
    {
        self::$output->writeln($message);
    }
}
