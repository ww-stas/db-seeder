<?php declare(strict_types=1);

namespace App;

class Mapper
{
    /**
     * @param array  $input
     * @param string $class
     *
     */
    public static function map(array $input, string $class): mixed
    {
        $instance = new $class;

        foreach ($input as $key => $value) {
            try {
                $instance->$key = $value;
            } catch (Throwable $e) {
                continue;
            }
        }

        return $instance;
    }

    public static function mapMany(array $input, string $class): array
    {
        $output = [];
        foreach ($input as $record) {
            if (!is_array($record)) {
                throw new \RuntimeException('The records should of array type');
            }
            $output[] = self::map($record, $class);
        }

        return $output;
    }


}
