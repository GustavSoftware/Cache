Cache
=====

A small and simple PHP cache system, which implements [PSR-6](http://www.php-fig.org/psr/psr-6/) interfaces.

[![Build Status](https://travis-ci.org/GustavSoftware/Cache.svg?branch=master)](https://travis-ci.org/GustavSoftware/Cache)


Implementations
===============

- **Filesystem**: Each cache item pool is a simple file. Cache items are saved with help of PHP's default serialization method.
- **Debug**: Cache item pools will not be persisted. This is just for testing purposes. Don't use this implementation in production environments.


Usage
=====

```php
//configure the system
$config = new \Gustav\Cache\Configuration();
$config->setDirectory("path/to/my/cache/files/");

//load cache manager
$manager = \Gustav\Cache\ACacheManager::getInstance($config);

//fetch the item pool
$pool = $manager->getItemPool("myFileName", $myCreatorFunction);

```


Configuration
=============

You can set the following configurations in `\Gustav\Orm\Configuration`:
- `setImplementation(string)`: Sets the implementation of the cache manager to use here. Default is `\Gustav\Cache\Filesystem\CacheManager` (i.e. the Filesystem cache).
- `setDirectory(string)`: Sets the directory on filesystem, where we save the cache pool files. There's no default value.
- `setDefaultExpiration(integer)`: Sets the default time to live of cache items, if you don't set any explicitly in `\Gustav\Cache\CacheItem`. Consider that `0` and lower means, that the items will not expire. Default ist `0`.