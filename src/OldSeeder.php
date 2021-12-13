<?php declare(strict_types=1);

namespace App;

use Faker\Factory;
use Faker\Generator;
use RuntimeException;

class OldSeeder
{
    private Connection $connection;
    private Generator $faker;
    /**
     * @var OldModel[]
     */
    private array $models = [];

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->faker = Factory::create();
    }

    public static function make(Connection $connection): static
    {
        return new static($connection);
    }

    /**
     * @param OldModel[] $models
     */
    public function setModels(array $models): static
    {
        $this->models = $models;

        return $this;
    }

    /**
     * @return OldModel[]
     */
    public function getModels(): array
    {
        return $this->models;
    }

    public function generate(string $class, int $count, ?array $params = null): static
    {
        $instance = new $class;
        if (!$instance instanceof OldModel) {
            throw new RuntimeException('The class should extends from App\\Model');
        }

        $models = [];
        for ($i = 0; $i < $count; $i++) {
            $models[] = array_merge($instance->value($this->faker), $params ?? []);
        }

        $this->insertMany($instance->tableName(), $models);

        return (new static($this->connection))->setModels(Mapper::mapMany($models, $class));
    }

    public function forEach(callable $callback): static
    {
        foreach ($this->models as $model) {
            $callback($this, $model);
        }

        return $this;
    }

//    private function insertMany(string $table, array $records): void
//    {
//        $chunks = array_chunk($records, 100);
//        if (empty($chunks)) {
//            return;
//        }
//        $pdo = $this->connection->getConnection();
//        $fields = array_keys($chunks[0][0]);
//
//        foreach ($chunks as $chunk) {
//            $query = $this->prepareQuery($table, $fields, $chunk);
//            $stmt = $pdo->prepare($query);
//            $stmt->execute($this->prepareValues($chunk));
//        }
//    }
//
//    private function prepareValues(array $chunk): array
//    {
//        $values = [];
//        foreach ($chunk as $row) {
//            foreach ($row as $value) {
//                $values[] = $value;
//            }
//        }
//
//        return $values;
//    }
//
//    private function prepareQuery(string $table, array $fields, array $values): string
//    {
//        $countOfValues = count($fields);
//        $val = '';
//        for ($i = 0, $count = count($values); $i < $count; $i++) {
//            $val .= '(';
//            for ($j = 0; $j < $countOfValues; $j++) {
//                $val .= '?';
//                if ($j < $countOfValues - 1) {
//                    $val .= ',';
//                }
//            }
//            $val .= ')';
//            if ($i < $count - 1) {
//                $val .= ',';
//            }
//        }
//
//        return sprintf('insert into %s (%s) values %s',
//            $table,
//            implode(',', $fields),
//            $val
//        );
//    }
//
//    private function formatFields(array $values): string
//    {
//        return sprintf('(%s)', implode(',', $values));
//    }
}
