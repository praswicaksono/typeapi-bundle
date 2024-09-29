<?php

declare(strict_types=1);

namespace PRSW\TypeApiBundle\Router;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

final class TypeApiRouteLoader extends Loader
{
    private TypeApiRouteCollector $routeCollector;

    public function __construct(TypeApiRouteCollector $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->routeCollector->collect();
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'typeapi' === $type;
    }
}
