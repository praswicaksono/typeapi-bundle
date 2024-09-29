<?php

declare(strict_types=1);

namespace PRSW\TypeApiBundle\Tests\TestData;

use PRSW\TypeApiBundle\Attributes\Path;
use PRSW\TypeApiBundle\Attributes\TypeApi;

use PSX\Api\Attribute\Post;

#[TypeApi]
class CreatePostUsingExtendedPathAttribute
{
    #[Post]
    #[Path('/post', 'https', 'example.com')]
    public function create(): PostResponse
    {
        $post = new PostResponse();
        $post->name = 'test';

        return $post;
    }
}
