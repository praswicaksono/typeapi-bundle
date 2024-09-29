<?php

declare(strict_types=1);

namespace Pras\TypeApiBundle\Resolver;

use PSX\Api\Attribute\Body;
use PSX\Api\Attribute\Param;
use PSX\Api\Attribute\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

final class TypeApiValueResolver implements ValueResolverInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attrs = $argument->getAttributes();

        foreach ($attrs as $attr) {
            $value = match ($attr::class) {
                Param::class => $this->castValue($request->get($argument->getName()), $argument->getType()),
                Query::class => $this->castValue($request->query->get($argument->getName()), $argument->getType()),
                Body::class => (function (Request $request, ArgumentMetadata $argument): ?object {
                    $class = (string) $argument->getType();
                    if (!class_exists($class)) {
                        throw new \InvalidArgumentException(\sprintf('Class %s not found', $class));
                    }

                    return $this->deserializeJsonBody($request, $class);
                })($request, $argument),
                default => [],
            };

            yield $value;

            return;
        }
    }

    /**
     * @template T
     *
     * @param class-string<T> $target
     *
     * @return T|null
     */
    private function deserializeJsonBody(Request $request, string $target)
    {
        $format = $request->getContentTypeFormat();

        if ($format === null) {
            return null;
        }

        return $this->serializer->deserialize(
            $request->getContent(),
            $target,
            $format,
        );
    }

    private function castValue(mixed $value, ?string $type = null): mixed
    {
        return match (\gettype($value)) {
            'integer' => (int) $value,
            'string' => (string) $value,
            'double' => (float) $value,
            'bool' => (bool) $value,
            default => $value,
        };
    }
}
