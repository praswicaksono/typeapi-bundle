<?php

declare(strict_types=1);

namespace Pras\TypeApiBundle\DependencyInjection\Pass;

use Pras\TypeApiBundle\Router\TypeApiRouteCollector;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TypeApiCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $apiCollections = $container->findTaggedServiceIds('typeapi.api.collection');
        $routeCollector = $container->getDefinition(TypeApiRouteCollector::class);

        $classes = [];
        foreach ($apiCollections as $class => $attr) {
            $classes[] = $class;
        }

        $routeCollector->addMethodCall('setDefinitions', [$classes]);
    }
}
