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
                Body::class => $this->serializer->deserialize(
                    $request->getContent(),
                    (string) $argument->getType(),
                    'json',
                ),
                default => [],
            };

            yield $value;

            return;
        }
    }

    private function castValue(mixed $value, ?string $type = null): mixed
    {
        return match (gettype($value)) {
            'integer' => (int) $value,
            'string' => (string) $value,
            'double' => (double) $value,
            'bool' => (bool) $value,
            default => $value,
        };
    }
}
