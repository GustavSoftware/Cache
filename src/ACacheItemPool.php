<?php
/**
 * Gustav Cache - A small and simple PHP cache system.
 * Copyright (C) since 2014  Gustav Software
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Gustav\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * This is a common interface for all our implementations of cache item pools.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
abstract class ACacheItemPool implements CacheItemPoolInterface, CacheInterface
{
    /**
     * This array contains all the saved data in this cache file. Each array
     * element is an array of two elements:
     * - 'expires' - The UNIX timestamp of the expiration date of the cache data
     *               (or 0, if the data does not expire)
     * - 'value' - The cached value
     *
     * @var array
     */
    protected array $_data;
    
    /**
     * The data to be saved later.
     * 
     * @var CacheItem[]
     */
    protected array $_deferred = [];
    
    /**
     * The configuration of this cache item pool.
     *
     * @var Configuration
     */
    protected Configuration $_configuration;
    
    /**
     * Constructor of this class.
     *
     * @param array $data
     *   The data
     * @param Configuration $configuration
     *   The configuration data to use in this session
     */
    public function __construct(array $data, Configuration $configuration)
    {
        $this->_data = $data;
        $this->_configuration = $configuration;
    }
    
    /**
     * @inheritDoc
     */
    public function getItem($key): CacheItem
    {
        $key = $this->_validateKey($key);
        if(isset($this->_deferred[$key])) {
            return new CacheItem(
                $key,
                $this->_deferred[$key]->get(),
                true,
                $this,
                $this->_deferred[$key]->getExpiration()
            );
        } elseif(isset($this->_data[$key])) {
            return new CacheItem($key, $this->_data[$key]['value'], true, $this, $this->_data[$key]['expires']);
        } else { //missed the item
            return new CacheItem($key, null, false, $this, $this->getDefaultExpiration());
        }
    }
    
    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []): iterable
    {
        foreach($keys as $key) {
            yield $key => $this->getItem($key);
        }
    }
    
    /**
     * @inheritDoc
     */
    public function hasItem($key): bool
    {
        $key = $this->_validateKey($key);
        return isset($this->_data[$key]) && !$this->_isExpired($key);
    }
    
    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->_data = [];
        return $this->_persist();
    }
    
    /**
     * @inheritDoc
     */
    public function deleteItem($key): bool
    {
        $key = $this->_validateKey($key);
        if(!isset($this->_data[$key])) {
            return true;
        }
        unset($this->_data[$key]);
        return $this->_persist();
    }
    
    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        return $this->deleteMultiple($keys);
    }
    
    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        $this->_data[$item->getKey()] = [
            'value' => $item->get(),
            'expires' => $item->getExpiration()
        ];
        return $this->_persist();
    }
    
    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        if(!$item instanceof CacheItem) {
            return false;
        }
        $this->_deferred[$item->getKey()] = $item;
        return true;
    }
    
    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        $data = $this->_data;
        foreach($this->_deferred as $item) {
            $this->_data[$item->getKey()] = [
                'value' => $item->get(),
                'expires' => $item->getExpiration()
            ];
        }
        if(!$this->_persist()) {
            $this->_data = $data;
            return false;
        } else {
            $this->_deferred = [];
            return true;
        }
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        if($this->hasItem($key)) {
            return $this->getItem($key)->get();
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        if($ttl instanceof DateInterval) {
            $expires = (new DateTime("now"))->add($ttl);
        } elseif(is_int($ttl)) {
            $expires = new DateTime("now + {$ttl} seconds");
        } else {
            $expires = null;
        }
        return $this->save(new CacheItem($key, $value, true, $this, $expires));
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        return $this->deleteItem($key);
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null): iterable
    {
        foreach($keys as $key) {
            yield $key => $this->get($key, $default);
        }
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        if($ttl instanceof DateInterval) {
            $expires = (new DateTime("now"))->add($ttl);
        } elseif(is_int($ttl)) {
            $expires = new DateTime("now + {$ttl} seconds");
        } else {
            $expires = null;
        }

        $return = true;
        foreach($values as $key => $value) {
            $return = $return && $this->saveDeferred(new CacheItem($key, $value, true, $this, $expires));
        }
        return $return && $this->_persist();
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        foreach($keys as $key) {
            $key = $this->_validateKey($key);
            unset($this->_data[$key]);
        }
        return $this->_persist();
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        return $this->hasItem($key);
    }

    /**
     * Returns the configuration data of this cache item pool.
     *
     * @return Configuration
     *   The configuration data
     */
    public function getConfiguration(): Configuration
    {
        return $this->_configuration;
    }
    
    /**
     * Returns the default expiration time of a new cache item.
     *
     * @return DateTimeInterface|null
     *   The expiration time
     */
    public function getDefaultExpiration(): ?DateTimeInterface
    {
        if($this->_configuration->getDefaultExpiration() == 0) {
            return null;
        } else {
            return new DateTime("now + " . $this->_configuration->getDefaultExpiration() . " seconds");
        }
    }
    
    /**
     * Validates the given key and casts it to a string if possible.
     *
     * @param mixed $key
     * @return string
     * @throws InvalidKeyException
     */
    private function _validateKey($key): string 
    {
        if(!is_scalar($key) && !is_object($key) && !method_exists($key, "__toString")) {
            throw InvalidKeyException::invalidKey();
        }
        return (string) $key;
    }
    
    
    /**
     * Checks whether the given data with the given key is expired (true) or
     * just valid (false). Consider that these data will be deleted from
     * \Gustav\Cache\Filesystem\CachePool::$_data if they're expired. For better
     * performance these changes will not be persisted here.
     *
     * @param string $key
     *   The key to check for validity
     * @return bool
     *   true, if the data is expired, yet, otherwise false
     */
    private function _isExpired(string $key): bool
    {
        if(!is_null($this->_data[$key]['expires']) && $this->_data[$key]['expires'] <= new DateTime("now")) {
            unset($this->_data[$key]);
            return true;
        }
        return false;
    }
    
    /**
     * Saves this cache pool on file system.
     *
     * @return bool
     *   true, if saving was successful, otherwise false
     */
    abstract protected function _persist(): bool;
}