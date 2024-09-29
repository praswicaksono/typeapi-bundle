<?php

declare(strict_types=1);

namespace Pras\TypeApiBundle;

use Pras\TypeApiBundle\Attributes\TypeApi;
use Pras\TypeApiBundle\DependencyInjection\Pass\TypeApiCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @see https://symfony.com/doc/current/bundles/best_practices.html
 */
class TypeApiBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $container->parameters()
            ->set('typeapi.config', $config)
            ->set('typeapi.debug', true);

        $builder->registerAttributeForAutoconfiguration(
            TypeApi::class,
            function (ChildDefinition $definition, TypeApi $attr, \Reflector $reflectionn): void {
                $definition->addTag('typeapi.api.collection');
            },
        );
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new TypeApiCompilerPass());
    }
}
