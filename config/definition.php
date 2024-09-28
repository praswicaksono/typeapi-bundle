<?php

declare(strict_types=1);

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#configuration
 */
return static function (DefinitionConfigurator $definition): void {
    /** @phpstan-ignore-next-line */
    $definition
        ->rootNode()
            ->children()
                ->scalarNode('debug')->defaultTrue()->end()
            ->end()
        ->end()
    ;
};
