<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormShape\Writer\Tag;

use function preg_match;
use function sprintf;
use function str_starts_with;
use function trim;

/**
 * @internal
 *
 * @psalm-internal Kynx\Laminas\FormShape
 * @psalm-internal KynxTest\Laminas\FormShape
 */
final readonly class PsalmImportType implements TagInterface
{
    public function __construct(private string $type, private string $from)
    {
    }

    public function __toString(): string
    {
        return sprintf('@psalm-import-type %s from %s', $this->type, $this->from);
    }

    public function isBefore(TagInterface $tag): bool
    {
        return str_starts_with(trim((string) $tag), '@psalm');
    }

    public function matches(TagInterface $tag): bool
    {
        return (bool) preg_match(
            '/^\s*@psalm-import-type\s+' . $this->type . '\s+from\s+' . $this->from . '/s',
            (string) $tag
        );
    }
}
