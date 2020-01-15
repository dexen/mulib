# dexen/mulib

Fast UUID v4 generator for PHP: 1MM in 0.7 second.

LZ4 decompressor in pure PHP, good for about 30MB/s. For sake of simplicity checksums are not verified.

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
$decompressed = \dexen\mulib\Lz4::decompress(file_get_contents('my-data.lz4'));
```
An lz4cat replacement is available for shell use:
```
lz4cat-php < my-data.lz4 > my-data
```
Consider defining a helper
```php
function generate_uuid_v4() : string
{
	return \dexen\mulib\Uuid::generateUuidV4();
}
```
