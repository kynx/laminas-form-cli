<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormShape\Command;

use Kynx\Laminas\FormShape\File\FormReader;
use Kynx\Laminas\FormShape\Form\FormVisitorInterface;
use Kynx\Laminas\FormShape\UnionDecoratorInterface;
use Psr\Container\ContainerInterface;

final readonly class PsalmTypeCommandFactory
{
    public function __invoke(ContainerInterface $container): PsalmTypeCommand
    {
        return new PsalmTypeCommand(
            $container->get(FormReader::class),
            $container->get(FormVisitorInterface::class),
            $container->get(UnionDecoratorInterface::class)
        );
    }
}