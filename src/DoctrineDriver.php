<?php declare(strict_types=1);

namespace App;

use App\Config\ModelConfig;

class DoctrineDriver implements ConnectionDriver
{
    private \Doctrine\DBAL\Connection $connection;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Model[] $models
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function insertMany(array $models): void
    {
        $table = $models[0]->getModelName();
        $chunks = array_chunk($models, 100);
        if (empty($chunks)) {
            return;
        }

        $columns = array_keys($chunks[0][0]->getFields());

        foreach ($chunks as $chunk) {
            /** @var Model[] $chunk */
            $valuesPlaceholder = $this->prepareQuery($columns, $chunk);
            $query = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ')' . ' VALUES ' . $valuesPlaceholder;
            $key = "insert to table ".$table;
            Metric::start($key);
            $this->connection->executeStatement($query, $this->flatMap($chunk));
            Metric::stop($key);
            Counter::getInstance()->update(count($chunk));
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
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
        $retVal = [];
        foreach ($models as $model) {
            foreach ($model->getFields() as $field) {
                $retVal[] = $field;
            }
        }

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

        return $val;
    }

}
