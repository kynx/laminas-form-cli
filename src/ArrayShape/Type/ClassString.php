<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormCli\ArrayShape\Type;

use function ltrim;

/**
 * @psalm-import-type VisitedArray from AbstractVisitedType
 */
final readonly class ClassString extends AbstractVisitedType
{
    /**
     * @param class-string $classString
     */
    public function __construct(public string $classString)
    {
    }

    public function getTypeString(int $indent = 0, string $indentString = '    '): string
    {
        return '\\' . ltrim($this->classString, '\\');
    }

    /**
     * @param VisitedArray $types
     */
    public function matches(array $types): bool
    {
        foreach ($types as $type) {
            if ($type instanceof ClassString && $type->classString === $this->classString) {
                return true;
            }
        }

        return false;
    }
}
