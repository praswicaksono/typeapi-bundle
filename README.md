# TypeapiBundle

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require pras/typeapi-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require pras/typeapi-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Pras\TypeApiBundle\TypeApiBundle::class => ['all' => true],
];
```

## Usage

Add TypeApi routes in `config/routes.yml`

```yml
typeapi:
    resource:
        path: .
    type: typeapi
```

Now you can use TypeApi attribute to define your API definition in your class, you mush add `TypeApi` attribute in your class in order to autoload them in symfony routes.

```php
<?php
declare(strict_types=1);

namespace App\Api;

use App\Dto\Hello;
use App\Dto\Payload;
use Pras\TypeApiBundle\Attributes\Path;
use Pras\TypeApiBundle\Attributes\TypeApi;
use PSX\Api\Attribute\Body;
use PSX\Api\Attribute\Post;

#[TypeApi]
final class PostCollection
{
    #[Post]
    #[Path('/hello')]
    public function hello(
        #[Body]
        Payload $payload
    ): Hello {
        return Hello::create($payload->name, '', $payload->id);
    }
}
```

All is setup to verify route already registered or not you check via `./bin/console debug:router`
