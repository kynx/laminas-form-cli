<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormCli\ArrayShape\Validator\Sitemap;

use Kynx\Laminas\FormCli\ArrayShape\Type\PsalmType;
use Kynx\Laminas\FormCli\ArrayShape\ValidatorParserInterface;
use Laminas\Validator\Sitemap\Priority;
use Laminas\Validator\ValidatorInterface;

final readonly class PriorityParser implements ValidatorParserInterface
{
    public function getTypes(ValidatorInterface $validator, array $existing): array
    {
        if (! $validator instanceof Priority) {
            return $existing;
        }

        $existing = PsalmType::replaceStringTypes($existing, [PsalmType::NumericString]);

        return PsalmType::filter($existing, [PsalmType::Int, PsalmType::Float, PsalmType::NumericString]);
    }
}
