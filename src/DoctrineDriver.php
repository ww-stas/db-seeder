<?php declare(strict_types=1);

namespace App;

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
     * @throws \Doctrine\DBAL\Exception
     */
    public function insertMany(string $table, array $records): void
    {
        $chunks = array_chunk($records, 100);
        if (empty($chunks)) {
            return;
        }

        $columns = array_keys($chunks[0][0]->getFields());

        foreach ($chunks as $chunk) {
            /** @var Model[] $chunk */
            $valuesPlaceholder = $this->prepareQuery($columns, $chunk);
            $query = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ')' . ' VALUES ' . $valuesPlaceholder;
            $this->connection->executeStatement($query, $this->flatMap($chunk));
        }
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
