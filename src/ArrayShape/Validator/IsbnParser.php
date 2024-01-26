<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormCli\ArrayShape\Validator;

use Kynx\Laminas\FormCli\ArrayShape\Type\PsalmType;
use Kynx\Laminas\FormCli\ArrayShape\ValidatorParserInterface;
use Laminas\Validator\Isbn;
use Laminas\Validator\ValidatorInterface;

final readonly class IsbnParser implements ValidatorParserInterface
{
    /**
     * @inheritDoc
     */
    public function getTypes(ValidatorInterface $validator, array $existing): array
    {
        if (! $validator instanceof Isbn) {
            return $existing;
        }

        return PsalmType::filter($existing, [
            PsalmType::Int,
            PsalmType::PositiveInt,
            PsalmType::String,
            PsalmType::NonEmptyString,
        ]);
    }
}
