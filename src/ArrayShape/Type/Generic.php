<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormCli\ArrayShape\Type;

use function array_map;
use function implode;
use function sort;

use const SORT_STRING;

final readonly class Generic extends AbstractVisitedType
{
    public function __construct(public PsalmType|ClassString $type, public array $union)
    {
    }

    public function getTypeString(int $indent = 0, string $indentString = '    '): string
    {
        if ($this->union === []) {
            return $this->type->getTypeString(0, $indentString);
        }
        $union = array_map(
            static fn (TypeStringInterface $type): string => $type->getTypeString(),
            $this->union
        );

        sort($union, SORT_STRING);

        return $this->type->getTypeString() . '<' . implode('|', $union) . '>';
    }
}
