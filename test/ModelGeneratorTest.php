<?php declare(strict_types=1);

namespace App;

use App\Config\ModelConfig;
use App\Mapper\ConfigMapper;
use PHPUnit\Framework\TestCase;

class ModelGeneratorTest extends TestCase
{
    public function test01ShouldGenerateModel(): void
    {
        //Given
        $mapper = new ConfigMapper();
        $modelConfig = $mapper->map(ModelConfig::class, [
            'table'   => 'users',
            'columns' => [
                'id'         => 'faker::uuid',
                'username'   => 'faker::name',
                'email'      => 'faker::email',
                'password'   => 'faker::password',
                'created_on' => 'faker::dateTimeBetween::-1 month|format::Y-m-d H:i:s',
                'updated_on' => '$var::now::Y-m-d H:i:s',
            ],
        ]);
        $modelGenerator = new ModelGenerator($modelConfig);

        //When
        $result = $modelGenerator->generate();

        //Then
        self::assertCount(count($modelConfig->columns), $result->getFields());
        self::assertEquals($modelConfig->table, $result->getModelName());
    }
}
