<?php

declare(strict_types=1);

namespace PRSW\TypeApiBundle\Tests\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PRSW\TypeApiBundle\Resolver\TypeApiValueResolver;
use PRSW\TypeApiBundle\Tests\TestData\PostResponse;
use PSX\Api\Attribute\Body;
use PSX\Api\Attribute\Param;
use PSX\Api\Attribute\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

class TypeApiValueResolverTest extends TestCase
{
    private SerializerInterface|MockObject $serializer;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function test_able_to_resolve_path_param_with_string_cast()
    {
        $resolver = new TypeApiValueResolver($this->serializer);

        $request = new Request();
        $request->request->set('name', 'pras');
        $argumentMetaData = new ArgumentMetadata('name', 'string', false, false, '', attributes: [new Param('name')]);

        $this->assertEquals('pras', iterator_to_array($resolver->resolve($request, $argumentMetaData))[0]);
    }

    public function test_able_to_resolve_path_param_with_int_cast()
    {
        $resolver = new TypeApiValueResolver($this->serializer);

        $request = new Request();
        $request->request->set('id', 1);
        $argumentMetaData = new ArgumentMetadata('id', 'integer', false, false, '', attributes: [new Param('id')]);

        $this->assertEquals(1, iterator_to_array($resolver->resolve($request, $argumentMetaData))[0]);
    }

    public function test_able_to_resolve_path_param_with_double_cast()
    {
        $resolver = new TypeApiValueResolver($this->serializer);

        $request = new Request();
        $request->request->set('score', 1.3);
        $argumentMetaData = new ArgumentMetadata('score', 'double', false, false, '', attributes: [new Param('score')]);

        $this->assertEquals(1.3, iterator_to_array($resolver->resolve($request, $argumentMetaData))[0]);
    }

    public function test_able_to_resolve_query_param_with_double_cast()
    {
        $resolver = new TypeApiValueResolver($this->serializer);

        $request = new Request();
        $request->query->set('score', 1.3);
        $argumentMetaData = new ArgumentMetadata('score', 'double', false, false, '', attributes: [new Query('score')]);

        $this->assertEquals(1.3, iterator_to_array($resolver->resolve($request, $argumentMetaData))[0]);
    }

    public function test_able_to_resolve_query_param_with_int_cast()
    {
        $resolver = new TypeApiValueResolver($this->serializer);

        $request = new Request();
        $request->query->set('id', 1);
        $argumentMetaData = new ArgumentMetadata('id', 'integer', false, false, '', attributes: [new Query('id')]);

        $this->assertEquals(1, iterator_to_array($resolver->resolve($request, $argumentMetaData))[0]);
    }

    public function test_able_to_resolve_query_param_with_string_cast()
    {
        $resolver = new TypeApiValueResolver($this->serializer);

        $request = new Request();
        $request->query->set('name', 'pras');
        $argumentMetaData = new ArgumentMetadata('name', 'string', false, false, '', attributes: [new Query('name')]);

        $this->assertEquals('pras', iterator_to_array($resolver->resolve($request, $argumentMetaData))[0]);
    }

    public function test_able_to_resolve_body()
    {
        $resolver = new TypeApiValueResolver($this->serializer);

        $request = new Request(content: '{"name": "pras"}');
        $request->headers->set('content-type', 'application/json');

        $argumentMetaData = new ArgumentMetadata('body', PostResponse::class, false, false, '', attributes: [new Body]);

        $ret = new PostResponse();
        $ret->name = "pras";
        $this->serializer->method('deserialize')->withAnyParameters()->willReturn($ret);

        $res = iterator_to_array($resolver->resolve($request, $argumentMetaData))[0];

        $this->assertEquals('pras', $res->name);
    }
}
