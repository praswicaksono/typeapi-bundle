<?php

declare(strict_types=1);

namespace Pras\TypeApiBundle\Router;

use Pras\TypeApiBundle\Attributes\Path;
use PSX\Api\ApiManagerInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class TypeApiRouteCollector
{
    /**
     * @var array<class-string>
     */
    private array $definitions;

    private ApiManagerInterface $apiManager;

    public function __construct(ApiManagerInterface $apiManager)
    {
        $this->apiManager = $apiManager;
    }

    public function setDefinitions(array $definitions): void
    {
        $this->definitions = $definitions;
    }

    public function collect(): RouteCollection
    {
        $spec = new Specification();
        $routes = new RouteCollection();

        foreach ($this->definitions as $definition) {
            $classSpec = $this->apiManager->getApi($definition);
            $routes->addCollection($this->convertSpecToRoute($definition, $classSpec));
            $spec->merge(
                $this->apiManager->getApi($definition),
            );
        }

        return $routes;
    }

    /**
     * @param class-string $class
     */
    private function convertSpecToRoute(string $class, SpecificationInterface $typeApiSpec): RouteCollection
    {
        $operations = $typeApiSpec->getOperations();
        $routes = new RouteCollection();

        foreach ($operations->getAll() as $name => $operation) {
            $path = (string) preg_replace('/:(\w+)/', '{$1}', $operation->getPath());
            [, $operationName] = explode('.', $name);

            $classReflector = new \ReflectionClass($class);

            $pathAttribute = $classReflector->getMethod($operationName)->getAttributes(Path::class)[0]->newInstance();

            $route = new Route(
                $path,
                [
                    '_controller' => "{$class}::{$operationName}",
                ],
                host: $pathAttribute->host,
                schemes: $pathAttribute->scheme ?? [],
                methods: [$operation->getMethod()],
                condition: $pathAttribute->condition,
            );
            $routes->add($name, $route);
        }

        return $routes;
    }
}
