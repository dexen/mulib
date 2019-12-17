# dexen/mulib

Fast UUID v4 generator for PHP: 1MM in 0.7 second.

[Composer](https://packagist.org/packages/dexen/mulib) and [PSR-4](https://www.php-fig.org/psr/psr-4/) friendly.

## Installation
```
composer require dexen/mulib
```

## Usage
Basic usage:
```php
$uuid_v4 = \dexen\mulib\Uuid::generateUuidV4();
$uuid_v5 = \dexen\mulib\Uuid::generateUuidV5($namespace_uuid, $name);
```
Consider defining a helper
```php
function generate_uuid_v4() : string
{
	return \dexen\mulib\Uuid::generateUuidV4();
}
```
