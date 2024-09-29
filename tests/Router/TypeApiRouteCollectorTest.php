<?php

declare(strict_types=1);

namespace PRSW\TypeApiBundle\Tests\Router;

use PHPUnit\Framework\TestCase;
use PRSW\TypeApiBundle\Router\TypeApiRouteCollector;
use PRSW\TypeApiBundle\Tests\TestData\CreatePost;
use PRSW\TypeApiBundle\Tests\TestData\CreatePostUsingExtendedPathAttribute;
use PSX\Api\ApiManager;
use PSX\Api\ApiManagerInterface;
use PSX\Api\Parser\Attribute\Builder;
use PSX\Schema\SchemaManager;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class TypeApiRouteCollectorTest extends TestCase
{
    private ApiManagerInterface $apiManager;

    protected function setUp(): void
    {
        $cache = new ArrayAdapter();
        $this->apiManager = new ApiManager(
            new SchemaManager(),
            new Builder($cache, false),
            $cache
        );
    }
    public function test_collector_using_default_attribute()
    {
        $collector = new TypeApiRouteCollector($this->apiManager);
        $collector->setDefinitions([CreatePost::class]);

        $routes = $collector->collect();

        $this->assertCount(1, $routes);
        $this->assertEquals('/post', $routes->get('tests.test_data.create_post.create')->getPath());
    }

    public function test_collector_using_extended_attribute()
    {
        $collector = new TypeApiRouteCollector($this->apiManager);
        $collector->setDefinitions([CreatePostUsingExtendedPathAttribute::class]);

        $routes = $collector->collect();

        $this->assertCount(1, $routes);
        $this->assertEquals('/post', $routes->get('tests.test_data.create_post_using_extended_path_attribute.create')->getPath());
        $this->assertEquals('https', $routes->get('tests.test_data.create_post_using_extended_path_attribute.create')->getSchemes()[0]);
        $this->assertEquals('example.com', $routes->get('tests.test_data.create_post_using_extended_path_attribute.create')->getHost());
    }
}
