<?php

declare(strict_types=1);

namespace KynxTest\Laminas\FormShape\Type;

use Kynx\Laminas\FormShape\Type\PsalmType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PsalmType::class)]
final class PsalmTypeTest extends TestCase
{
    public function testGetTypeStringReturnsType(): void
    {
        $expected = 'non-empty-array';
        $actual   = PsalmType::NonEmptyArray->getTypeString();
        self::assertSame($expected, $actual);
    }
}