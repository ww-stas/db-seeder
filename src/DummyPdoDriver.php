<?php declare(strict_types=1);

namespace App;

class DummyPdoDriver implements ConnectionDriver
{
    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function insertMany(string $table, array $records): void
    {
        $chunks = array_chunk($records, 100);
        if (empty($chunks)) {
            return;
        }
        $pdo = $this->connection->getConnection();
        $fields = array_keys($chunks[0][0]->getFields());

        foreach ($chunks as $chunk) {
            $query = $this->prepareQuery($table, $fields, $chunk);
            $stmt = $pdo->prepare($query);
            $stmt->execute($this->prepareValues($chunk));
        }
    }

    /**
     * @param Model[] $chunk
     *
     * @return array
     */
    private function prepareValues(array $chunk): array
    {
        $values = [];
        foreach ($chunk as $row) {
            $values[] = $row->getFields();
        }

        return $values;
    }

    /**
     * @param string  $table
     * @param array   $fields
     * @param Model[] $values
     *
     * @return string
     */
    private function prepareQuery(string $table, array $fields, array $values): string
    {
        $countOfValues = count($fields);
        $val = '';
        for ($i = 0, $count = count($values); $i < $count; $i++) {
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

        return sprintf('insert into %s (%s) values %s',
            $table,
            implode(',', $fields),
            $val
        );
    }

    private function formatFields(array $values): string
    {
        return sprintf('(%s)', implode(',', $values));
    }
}
