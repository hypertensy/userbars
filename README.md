# Generator userbars Warface

Simple and free library to generate userbars game Warface.

## 1. Prerequisites

* PHP **7.4** or later
* Imagick [module]

## 2. Installation

This generator can be installed using Composer by running the following command:

```sh
composer require wnull/userbars-warface-generator
```

## 3. Initialization

Import the required classes:

```php
require __DIR__ . '/vendor/autoload.php';

use WF\Draw\Paint;
use WF\Client\{Client, Enums\ServerList};
use WF\Achievement\Reveal;
```

## 4. Example of use

Before using, you should read a documentation about the functions and their parameters. 

#### 4.1. Basic usage:

  ```php
  $obj = (new Paint(new Client('JAWAR', ServerList::US)))->display();
  ```

#### 4.2. Use with the addition of game achievements
 
Use the `Reveal` class and pass only an array with keys: `mark`, `badge`, `stripe`.

   ```php
  $obj = (new Paint(new Client('Эдия', ServerList::ALPHA), new Reveal(['mark' => 417, 'stripe' => 6524])))->display();
   ```
   
 After that, an image object (Imagick) is created, which can either be displayed or written to a file.

## 5. Result

Examples of generated images:

![EN](https://user-images.githubusercontent.com/33278849/78178484-6483c700-7468-11ea-9129-ca4a94a9b383.png)
![RU](https://user-images.githubusercontent.com/33278849/78177925-8c265f80-7467-11ea-9374-b157b484245c.png)

## License

This library is licensed under the [MIT License](https://github.com/wnull/userbars-warface-generator/blob/master/LICENSE).