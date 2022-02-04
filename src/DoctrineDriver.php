<?php declare(strict_types=1);

namespace App;

use App\Config\ModelConfig;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class DoctrineDriver implements ConnectionDriver
{
    private Connection $connection;
    private const CHUNK_SIZE = 50;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Model[] $models
     *
     * @throws Exception
     */
    public function insertMany(array $models): void
    {
        $table = $models[0]->getModelName();
        $key = "insert to table ".$table;
        Metric::start($key);
        $chunks = array_chunk($models, self::CHUNK_SIZE);
        if (empty($chunks)) {
            return;
        }

        $columns = array_keys($chunks[0][0]->getFields());

        foreach ($chunks as $chunk) {
            /** @var Model[] $chunk */
            $valuesPlaceholder = $this->prepareQuery($columns, $chunk);
            $query = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ')' . ' VALUES ' . $valuesPlaceholder;
            $this->connection->executeStatement($query, $this->flatMap($chunk));
            Counter::getInstance()->update(count($chunk));
        }
        Metric::stop($key);
    }

    /**
     * @throws Exception
     */
    public function isTableExists(string $table): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        return $schemaManager->tablesExist($table);
    }

    public function select(ModelConfig $model, ?array $condition): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder = $queryBuilder->select('*')->from($model->table);
        if ($condition !== null) {
            $predicate = $condition[0] . ' ' . $condition[1] . ' ?';
            $queryBuilder = $queryBuilder->where($predicate)->setParameter(0, $condition[2]);
        }
        return $queryBuilder->fetchAllAssociative();
    }

    /**
     * @param Model[] $models
     *
     * @return array
     */
    private function flatMap(array $models): array
    {
        Metric::start("flatMap");
        $retVal = [];
        foreach ($models as $model) {
            foreach ($model->getFields() as $field) {
                $retVal[] = $field;
            }
        }

        Metric::stop("flatMap");
        return $retVal;
    }

    /**
     * @param array   $fields
     * @param Model[] $models
     *
     * @return string
     */
    private function prepareQuery(array $fields, array $models): string
    {
        Metric::start("prepareQuery");
        $countOfValues = count($fields);

        $val = '';
        for ($i = 0, $count = count($models); $i < $count; $i++) {
            $val .= '(';
            for ($j = 0; $j < $countOfValues; $j++) {
                $val .= '?';
                if ($j < $countOfValues - 1) {
                    $val .= ',';
                }
            }
            $val .= ')';
            if ($i < $count - 1) {
                $val .= ',';
            }
        }

        Metric::stop("prepareQuery");
        return $val;
    }
}
