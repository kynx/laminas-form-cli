<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormShape\Command;

use Kynx\Laminas\FormShape\ArrayShapeException;
use Kynx\Laminas\FormShape\Decorator\ArrayShapeDecorator;
use Kynx\Laminas\FormShape\File\FormReaderInterface;
use Kynx\Laminas\FormShape\InputFilterVisitorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FormShapeCommand extends Command
{
    public function __construct(
        private readonly FormReaderInterface $formProcessor,
        private readonly InputFilterVisitorInterface $inputFilterVisitor,
        private readonly ArrayShapeDecorator $decorator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription("Generate array shape for form")
            ->addArgument('path', InputArgument::REQUIRED, 'Path to form');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('path');
        $io   = new SymfonyStyle($input, $output);

        $formFile = $this->formProcessor->getFormFile($path);
        if ($formFile === null) {
            $io->error("Cannot find form at path '$path'");
            return self::INVALID;
        }

        try {
            $arrayShape = $this->inputFilterVisitor->visit($formFile->form->getInputFilter());
        } catch (ArrayShapeException $e) {
            $io->error($e->getMessage());
            return self::FAILURE;
        }

        $io->section("Psalm type for $path");
        $io->block($this->decorator->decorate($arrayShape));
        return self::SUCCESS;
    }
}