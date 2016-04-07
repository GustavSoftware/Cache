<?php

/*
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

/**
 * This is a common interface for management of all the cache item pools.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
abstract class ACacheManager
{
    /**
     * The configuration data of this cache manager.
     *
     * @var \Gustav\Cache\Configuration
     */
    protected $_configuration;
    
    /**
     * Constructor of this class.
     *
     * @param \Gustav\Cache\Configuration $configuration
     *   The configuration of this cache manager
     */
    protected function __construct(Configuration $configuration) 
    {
        $this->_configuration = $configuration;
    }

    /**
     * Returns a new instance of this cache manager.
     *
     * @param \Gustav\Cache\Configuration $configuration
     *   The configuration of this cache manager
     * @return \Gustav\Cache\ACacheManager
     *   The cache manager
     */
    final public static function getInstance(
        Configuration $configuration
    ): ACacheManager {
        $implementation = $configuration->getImplementation();
        return new $implementation($configuration);
    }
    
    /**
     * Returns the configuration data of this cache manager.
     *
     * @return \Gustav\Cache\Configuration
     *   The configuration data
     */
    public function getConfiguration(): Configuration 
    {
        return $this->_configuration;
    }

    /**
     * Loads and returns the cache item pool with the given name. If this pool
     * does not exist this method will create a new one.
     *
     * @param string $fileName
     *   The name of the item pool's file
     * @param callable|null $creator
     *   An additional operation for creation of the cache file if it does not
     *   exist, yet
     * @return \Gustav\Cache\ACacheItemPool
     *   The cache item pool
     * @throws \Gustav\Cache\CacheException
     *   File not readable or bad file name
     */
    abstract public function getItemPool(
        string $fileName,
        callable $creator = null
    ): ACacheItemPool;
}