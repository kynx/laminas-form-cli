<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormShape\InputFilter;

use Kynx\Laminas\FormShape\InputVisitorInterface;
use Laminas\InputFilter\InputInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Union;

final readonly class CollectionInputVisitor implements InputVisitorInterface
{
    public function __construct(private InputVisitor $inputVisitor)
    {
    }

    public function visit(InputInterface $input): ?Union
    {
        if (! $input instanceof CollectionInput) {
            return null;
        }

        $union = $this->inputVisitor->visit($input);
        $array = $input->getCount() > 0
            ? new TNonEmptyArray([Type::getArrayKey(), new Union($union->getAtomicTypes())])
            : new TArray([Type::getArrayKey(), new Union($union->getAtomicTypes())]);

        return new Union([$array], ['possibly_undefined' => $input->isPossiblyUndefined()]);
    }
}