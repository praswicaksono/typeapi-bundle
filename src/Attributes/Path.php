<?php

declare(strict_types=1);

namespace Pras\TypeApiBundle\Attributes;

use PSX\Api\Attribute\Path as BasePath;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Path extends BasePath
{
    public function __construct(
        string $path,
        public ?string $scheme = null,
        public ?string $host = null,
        public ?string $condition = null,
    ) {
        parent::__construct($path);
    }
}
