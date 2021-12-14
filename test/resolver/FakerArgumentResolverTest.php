<?php declare(strict_types=1);

namespace App\Resolver;

use PHPUnit\Framework\TestCase;
§
class FakerArgumentResolverTest extends TestCase
{
    public function test01ArrayArgument(): void
    {
        //Given
        $resolver = new FakerArgumentResolver('numberBetween', [2, 5]);

        //When
        $result = $resolver->resolve(null);

        //Then
        self::assertIsNumeric($result);
        self::assertGreaterThanOrEqual(2, $result);
        self::assertLessThanOrEqual(5, $result);
    }

    public function test02ArrayArgument(): void
    {
        //Given
        $resolver = new FakerArgumentResolver('randomElement', [['NEW', 'APPROVED']]);

        //When
        $result = $resolver->resolve(null);

        //Then
        self::assertIsString($result);
        self::assertContains($result, ['NEW', 'APPROVED']);
    }

    public function test03UuidResolver(): void
    {
        //Given
        $resolver = new FakerArgumentResolver('uuid');

        //When
        $result = $resolver->resolve(null);

        //Then
        self::assertIsString($result);
        self::assertStringMatchesFormat('%x-%x-%x-%x-%x', $result);
    }

    public function test04DateResolver(): void
    {
        //Given
        $resolver = new FakerArgumentResolver('date', 'Y-m-d H:i:s');

        //When
        $result = $resolver->resolve(null);

        //Then
        self::assertIsString($result);
        self::assertStringMatchesFormat('%d-%d-%d %d:%d:%d', $result);
    }

    //faker::dateTimeBetween::-1 month|format::Y-m-d H:i:s
    public function test05DateRangWithPipeFormat(): void
    {
        //Given
        $after = new FormatArgumentResolver('Y-m-d H:i:s');
        $resolver = new FakerArgumentResolver('dateTimeBetween', '-1 month');
        $resolver->setAfter($after);

        //When
        $result = $resolver->resolve(null);

        //Then
        self::assertIsString($result);
        self::assertStringMatchesFormat('%d-%d-%d %d:%d:%d', $result);
    }
}
