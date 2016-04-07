<?php

/*
 * Gustav ORM - A simple PHP framework for object-relational mappings.
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

use Psr\Cache\CacheItemInterface;

/**
 * This class represents single cache items.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheItem implements CacheItemInterface
{
    /**
     * The key of this cache item
     *
     * @var string
     */
    private $_key;

    /**
     * The value of this cache item
     *
     * @var mixed
     */
    private $_value;

    /**
     * The time of the expiration of this item. If this is NULL, the default
     * expiration time should be used.
     *
     * @var \DateTimeInterface|null
     */
    private $_expiration;

    /**
     * @var boolean
     */
    private $_hit;

    /**
     * The owning cache item pool.
     *
     * @var \Gustav\Cache\ACacheItemPool
     */
    private $_pool;

    /**
     * Constructor of this class.
     *
     * @param string $key
     *   The key of this cache item
     * @param mixed $value
     *   The value of this cache item
     * @param \DateTimeInterface|null $expiration
     *   The time of expiration of this item
     * @param boolean $hit
     *   true, if this was a cache hit, otherwise false (i.e. cache miss)
     * @param \Gustav\Cache\ACacheItemPool $pool
     *   The owning cache item pool
     */
    public function __construct(
        string $key,
        $value,
        \DateTimeInterface $expiration,
        bool $hit,
        ACacheItemPool $pool
    ) {
        $this->_key = $key;
        $this->_expiration = $expiration;
        if($this->_isExpired()) {
            $this->_value = null;
            $this->_hit = false;
        } else {
            $this->_value = $value;
            $this->_hit = true;
        }
        $this->_pool = $pool;
    }
    
    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * @inheritdoc
     */
    public function get()
    {
        if($this->isHit()) {
            return $this->_value;
        }
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public function isHit()
    {
        return $this->_hit && !$this->_isExpired();
    }
    
    /**
     * @inheritdoc
     */
    public function set($value)
    {
        $this->_value = $value;
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function expiresAt($expiration)
    {
        if(\is_null($expiration)) {
            $this->_expiration = $this->_pool->getDefaultExpiration();
        } else {
            assert($expiration instanceof \DateTimeInterface);
            $this->_expiration = $expiration;
        }
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function expiresAfter($time)
    {
        if(\is_null($time)) {
            $this->_expiration = $this->_pool->getDefaultExpiration();
        } elseif(\is_numeric($time)) {
            $this->_expiration = new \DateTime("now + " . $time . " seconds");
        } else {
            assert($time instanceof \DateInterval);
            $expiration = new \DateTime("now");
            $expiration->add($time);
            $this->_expiration = $expiration;
        }
        return $this;
    }
    
    /**
     * Returns the time of expiration of this item.
     * 
     * @return \DateTimeInterface|null
     *   The time of expiration
     */
    public function getExpiration()
    {
        return $this->_expiration;
    }

    /**
     * Checks whether the given data with the given key is expired (true) or
     * just valid (false).
     *
     * @return boolean
     *   true, if the data is expired, yet, otherwise false
     */
    private function _isExpired(): bool
    {
        return (
            !\is_null($this->_expiration) &&
            $this->_expiration <= new \DateTime("now")
        );
    }
}