<?php declare(strict_types=1);

namespace Test\Config;

use App\ConfigMapper;
use App\ValidationException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Test\Config\Model\Example1\Config;
use Test\Config\Model\Example1\Nested;

class TestConfig extends TestCase
{

    /**
     * @throws ReflectionException
     * @throws ValidationException
     */
    public function testParseNestedObjects(): void
    {
        $class = Config::class;

        $t = new Config();
        $t ->setNested((new Nested())->setField("value"));

        /** @var Config $result */
        $result = ConfigMapper::make()->map($class, __DIR__ . '/model/example1/config.yml');

        self::assertInstanceOf($class, $result);
        self::assertNotNull($result->getNested());
        self::assertEquals('value', $result->getNested()->getField());

    }
}


