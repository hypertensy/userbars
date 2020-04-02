# Documentation

The main documentation for the classes and methods used in this library is provided below.

## Class - Client (required)

This class is secondary and is used as a closure in the constructor of the `Paint` class. You need it to get the necessary player data via the official API.
```php
Client::__construct ( string $nickname , int $server )
```
The required parameters are passed to the constructor as arguments: `$nickname` of the `string` type, and `$server` of the `int`type.

## Class - Reveal (optional)

This class is necessary in order to custom userbar can be added in-game achievements.

```php
Reveal::__construct ( array $data )
```
To use it, you must import the dependency:
```php
use WF\Achievement\Reveal;
```
As an argument, an associative array is passed to the class constuctor that includes only the following keys: `mark`, `badge`, and `stripe`.

Structure of a valid array:

| Key           | Value     |
| ------------- | --------- |
| mark          | mark ID   |
| badge         | badge ID  |
| stripe        | stripe ID |

## Class - Paint (required)
This is the most important class that is responsible for generating the image itself.
```php
Paint::__construct ( Client $client [, Reveal $reveal = NULL ])
```
The first required argument is an instance of the `Client` class, and the second optional argument is an instance of the `Reveal`class.

* ### Method - display()
  ```php
  Paint::display ( void ): Imagick 
  ```
  This is a mandatory method that is used for generating and further displaying the object of the created image.

## Class - ServerList  (required)
```php
ServerList::const
```
This class consists entirely of constants that represent a specific game server.

| Константа | Значение | Сервер           |
| --------- | -------- | ---------------- | 
| ALPHA     | 1        | Альфа            |
| BRAVO     | 2        | Браво            |
| CHARLIE   | 3        | Чарли            |
| EU        | 4        | Европа           |
| US        | 5        | Северная Америка |

These constants are used as the second argument in the 'Client' class.
