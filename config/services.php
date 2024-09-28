<?php

declare(strict_types=1);

use Pras\TypeApiBundle\EventListener\SerializeTypeApiResponseListener;
use Pras\TypeApiBundle\Resolver\TypeApiValueResolver;
use Pras\TypeApiBundle\Router\TypeApiRouteLoader;
use Pras\TypeApiBundle\Router\TypeApiRouteCollector;
use PSX\Api\ApiManager;
use PSX\Api\ApiManagerInterface;
use PSX\Api\Generator\Spec\TypeAPI;
use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorInterface;
use PSX\Api\GeneratorRegistry;
use PSX\Api\Parser\Attribute\Builder;
use PSX\Schema\SchemaManager;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters->set('typeapi.debug', true);

    $services = $container->services();

    $services->set('typeapi.schema.cache', ArrayAdapter::class);

    $services->set(SchemaManager::class, SchemaManager::class);
    $services->set(Builder::class, Builder::class)
        ->args([service('typeapi.schema.cache'), param('typeapi.debug')]);
    $services->set(ApiManager::class, ApiManager::class)
        ->args([service(SchemaManager::class), service(Builder::class), service('typeapi.schema.cache'), param('typeapi.debug')]);
    $services->alias(ApiManagerInterface::class, ApiManager::class);
    $services->set('typeapi.schema_generator_factory.local', GeneratorRegistry::class)
        ->factory([GeneratorFactory::class, 'fromLocal'])
        ->call('factory');

    $services->set('typeapi.schema_generator.typeapi', TypeAPI::class)
        ->public()
        ->factory([service('typeapi.schema_generator_factory.local'), 'getGenerator'])
        ->args(['spec-typeapi']);
    $services->alias(GeneratorInterface::class, 'typeapi.schema_generator.typeapi');

    $services->set(TypeApiRouteCollector::class, TypeApiRouteCollector::class)
        ->args([service(ApiManagerInterface::class)]);

    $services->set(TypeApiRouteLoader::class, TypeApiRouteLoader::class)
        ->args([service(TypeApiRouteCollector::class)])
        ->tag('routing.loader');

    $services->set(TypeApiValueResolver::class, TypeApiValueResolver::class)
        ->autoconfigure(true)
        ->autowire(true)
        ->tag('controller.argument_value_resolver');

    $services->set(SerializeTypeApiResponseListener::class, SerializeTypeApiResponseListener::class)
        ->autoconfigure(true)
        ->autowire(true)
        ->tag('kernel.event_subscriber');
};
