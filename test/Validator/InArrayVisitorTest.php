<?php

declare(strict_types=1);

namespace KynxTest\Laminas\FormShape\Validator;

use Kynx\Laminas\FormShape\Type\Literal;
use Kynx\Laminas\FormShape\Type\PsalmType;
use Kynx\Laminas\FormShape\Type\TypeUtil;
use Kynx\Laminas\FormShape\Validator\InArrayVisitor;
use Laminas\Validator\Barcode;
use Laminas\Validator\InArray;
use Laminas\Validator\ValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-import-type VisitedArray from TypeUtil
 */
#[CoversClass(InArrayVisitor::class)]
final class InArrayVisitorTest extends TestCase
{
    /**
     * @param VisitedArray $existing
     */
    #[DataProvider('visitLiteralProvider')]
    public function testVisitReturnsLiteral(ValidatorInterface $validator, array $existing, array $expected): void
    {
        $visitor = new InArrayVisitor();
        $actual  = $visitor->visit($validator, $existing);
        self::assertEquals($expected, $actual);
    }

    public static function visitLiteralProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'invalid'         => [new Barcode(), [PsalmType::Bool], [PsalmType::Bool]],
            'empty'           => [new InArray(['haystack' => []]), [PsalmType::Bool], [PsalmType::Bool]],
            'strict null'     => [new InArray(['haystack' => [null], 'strict' => true]), [PsalmType::String, PsalmType::Null], [PsalmType::Null]],
            'lax null'        => [new InArray(['haystack' => [null], 'strict' => false]), [PsalmType::String, PsalmType::Null], [PsalmType::Null, PsalmType::String]],
            'strict string'   => [new InArray(['haystack' => ['foo'], 'strict' => true]), [PsalmType::String], [new Literal(["foo"])]],
            'lax string'      => [new InArray(['haystack' => ['foo'], 'strict' => false]), [PsalmType::String], [new Literal(["foo"])]],
            'strict int'      => [new InArray(['haystack' => [123], 'strict' => true]), [PsalmType::String, PsalmType::Int], [new Literal([123])]],
            'lax int'         => [new InArray(['haystack' => [123], 'strict' => false]), [PsalmType::String, PsalmType::Int], [new Literal([123, "123"])]],
            'strict float'    => [new InArray(['haystack' => [1.23], 'strict' => true]), [PsalmType::Float], [PsalmType::Float]],
            'lax float'       => [new InArray(['haystack' => [1.23], 'strict' => false]), [PsalmType::Float], [PsalmType::Float]],
            'strict multiple' => [new InArray(['haystack' => ['foo', null], 'strict' => true]), [PsalmType::String, PsalmType::Null], [PsalmType::Null, new Literal(["foo"])]],
            'lax multiple'    => [new InArray(['haystack' => ['foo', null], 'strict' => false]), [PsalmType::String, PsalmType::Null], [PsalmType::Null, PsalmType::String, new Literal(["foo"])]],
        ];
        // phpcs:enable
    }

    /**
     * @param VisitedArray $existing
     */
    #[DataProvider('visitTypeProvider')]
    public function testVisitReturnsType(ValidatorInterface $validator, array $existing, array $expected): void
    {
        $visitor = new InArrayVisitor(false, 0);
        $actual  = $visitor->visit($validator, $existing);
        self::assertEquals($expected, $actual);
    }

    public static function visitTypeProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'empty'         => [new InArray(['haystack' => []]), [PsalmType::String], []],
            'strict null'   => [new InArray(['haystack' => [null], 'strict' => true]), [PsalmType::String, PsalmType::Null], [PsalmType::Null]],
            'lax null'      => [new InArray(['haystack' => [null], 'strict' => false]), [PsalmType::String, PsalmType::Null], [PsalmType::Null, PsalmType::String]],
            'strict string' => [new InArray(['haystack' => ['foo'], 'strict' => true]), [PsalmType::String], [PsalmType::String]],
            'lax string'    => [new InArray(['haystack' => ['foo'], 'strict' => false]), [PsalmType::String], [PsalmType::String]],
            'strict int'    => [new InArray(['haystack' => [123], 'strict' => true]), [PsalmType::String, PsalmType::Int], [PsalmType::Int]],
            'lax int'       => [new InArray(['haystack' => [123], 'strict' => false]), [PsalmType::String, PsalmType::Int], [PsalmType::Int, PsalmType::String]],
            'strict float'  => [new InArray(['haystack' => [1.23], 'strict' => true]), [PsalmType::Float], [PsalmType::Float]],
            'lax float'     => [new InArray(['haystack' => [1.23], 'strict' => false]), [PsalmType::String, PsalmType::Float], [PsalmType::Float, PsalmType::String]],
        ];
        // phpcs:enable
    }
}