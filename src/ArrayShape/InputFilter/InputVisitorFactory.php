<?php

declare(strict_types=1);

namespace Kynx\Laminas\FormCli\ArrayShape\InputFilter;

use Kynx\Laminas\FormCli\ArrayShape\FilterVisitorInterface;
use Kynx\Laminas\FormCli\ArrayShape\InputFilter\InputVisitor;
use Kynx\Laminas\FormCli\ArrayShape\ValidatorVisitorInterface;
use Kynx\Laminas\FormCli\ConfigProvider;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * @psalm-import-type ConfigProviderArray from ConfigProvider
 */
final readonly class InputVisitorFactory
{
    public function __invoke(ContainerInterface $container): InputVisitor
    {
        /** @var ConfigProviderArray $config */
        $config = $container->get('config') ?? [];

        $filterVisitors = [];
        foreach ($config['laminas-form-cli']['array-shape']['filter-visitors'] as $visitorName) {
            $visitor = $this->getVisitor($container, $visitorName);
            assert($visitor instanceof FilterVisitorInterface);
            $filterVisitors[] = $visitor;
        }

        $validatorVisitors = [];
        foreach ($config['laminas-form-cli']['array-shape']['validator-visitors'] as $visitorName) {
            $visitor = $this->getVisitor($container, $visitorName);
            assert($visitor instanceof ValidatorVisitorInterface);
            $validatorVisitors[] = $visitor;
        }

        return new InputVisitor($filterVisitors, $validatorVisitors);
    }

    /**
     * @param class-string<FilterVisitorInterface|ValidatorVisitorInterface> $visitorName
     */
    private function getVisitor(
        ContainerInterface $container,
        string $visitorName
    ): FilterVisitorInterface|ValidatorVisitorInterface {
        if ($container->has($visitorName)) {
            return $container->get($visitorName);
        }

        return new $visitorName();
    }
}
