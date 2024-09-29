<?php

declare(strict_types=1);

namespace PRSW\TypeApiBundle\Tests\TestData;

use PRSW\TypeApiBundle\Attributes\TypeApi;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;

#[TypeApi]
final class CreatePost
{
    #[Post]
    #[Path('/post')]
    public function create(): PostResponse
    {
        $post = new PostResponse();
        $post->name = 'test';

        return $post;
    }
}
