# Generator userbars for Warface [![Latest Stable Version](https://poser.pugx.org/wnull/userbars-warface-generator/v)](//packagist.org/packages/wnull/userbars-warface-generator) [![Total Downloads](https://poser.pugx.org/wnull/userbars-warface-generator/downloads)](//packagist.org/packages/wnull/userbars-warface-generator) [![License](https://poser.pugx.org/wnull/userbars-warface-generator/license)](//packagist.org/packages/wnull/userbars-warface-generator)

Library for generating game userbars Warface on PHP.

## Prerequisites

| Name               | Version |
|  ---               |   ---   |
| php                | \>=7.4  |
| wnull/warface-api  |  ^2.0   |
| ext-imagick        |    *    |

## Installation

This generator can be installed using Composer by running the following command:

```sh
composer require wnull/wfub
```

## Example of use

Before using, you should read a documentation about the functions and their parameters. 

```php
// Plug-in dependencies through by Composer
require __DIR__ . '/vendor/autoload.php';

// Creating an instance of a class with a query
$client = new WFub\Draw('Сцена', Warface\Enums\GameServer::ALPHA);
// Generating an Imagick image object
$image = $client->create();
```

## License

This library is licensed under the [MIT License](https://github.com/wnull/warface-api/blob/master/LICENSE).
