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

use Gustav\Cache\Filesystem\CacheManager;
use Gustav\Utils\Log\Configuration as LogConfiguration;

/**
 * This class is used for some important configurations of this cache system.
 * 
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class Configuration 
{
    /**
     * This is the class name of the implementation of the cache manager that
     * should be used on runtime. Consider that this class has to extend the
     * \Gustav\Cache\ACacheManager class.
     *
     * @var string
     */
    private string $_implementation = CacheManager::class;
    
    /**
     * The absolute path to the directory which contains all the saved cache
     * files.
     *
     * @var string
     */
    private string $_dir = "";
    
    /**
     * The default time to live of a single cache item in seconds. If this
     * constant is not greater than 0 the cache items should never expire. 
     *
     * @var int
     */
    private int $_defaultExpiration = 0;

    /**
     * The configuration data of the logger to use here.
     *
     * @var \Gustav\Utils\Log\Configuration
     */
    private \Gustav\Utils\Log\Configuration $_logConfiguration;

    /**
     * Constructor of this class.
     */
    public function __construct()
    {
        $this->_logConfiguration = new LogConfiguration("cache");
    }

    /**
     * Sets the class name of the implementation of the cache manager that
     * should be used on runtime. Consider that this class name has to extent
     * the \Gustav\Cache\ACacheManager class. Otherwise this method will throw
     * an CacheException.
     *
     * @param string $className
     *   The class name of the implementation to use here
     * @return \Gustav\Cache\Configuration
     *   This object
     * @throws \Gustav\Cache\CacheException
     *   Invalid implementation
     */
    public function setImplementation(string $className): Configuration 
    {
        if(!\is_subclass_of($className, ACacheManager::class)) {
            throw CacheException::invalidImplementation($className);
        }
        $this->_implementation = $className;
        return $this;
    }
    
    /**
     * Returns the class name of the implementation of the cache manager that
     * should be used on runtime.
     *
     * @return string
     *   The class name of the implementation to use here
     */
    public function getImplementation(): string 
    {
        return $this->_implementation;
    }
    
    /**
     * Sets the directory where the cache files can be saved. Consider that this
     * path should be absolute. Consider that this isn't needed for some special
     * implementations.
     *
     * @param string $dir
     *   The directory where to save the cache files
     * @return \Gustav\Cache\Configuration
     *   This object
     */
    public function setDirectory(string $dir): Configuration 
    {
        $this->_dir = $dir;
        return $this;
    }
    
    /**
     * Returns the directory where the cache files can be saved. This path is
     * an absolute path.
     *
     * @return string
     *   The path where the cache files can be saved
     */
    public function getDirectory(): string 
    {
        return $this->_dir;
    }
    
    /**
     * Sets the default time to live of a single cache item. After this time
     * the items will not be valid anymore. If this value isn't greater than 0,
     * the cache items will not expire (if not set otherwise for the special
     * item).
     * 
     * @param integer $seconds
     *   The number of seconds until expiration
     * @return \Gustav\Cache\Configuration
     *   This object
     */
    public function setDefaultExpiration(int $seconds = 0): Configuration 
    {
        $this->_defaultExpiration = $seconds;
        return $this;
    }
    
    /**
     * Returns the default time to live of a single cache item. If this value
     * is not greater than 0 the cache items will not expire.
     * 
     * @return integer
     *   The number of seconds until expiration
     */
    public function getDefaultExpiration(): int 
    {
        return $this->_defaultExpiration;
    }

    /**
     * Sets the configuration of the logger to use here.
     *
     * @param \Gustav\Utils\Log\Configuration $configuration
     *   The new configuration data
     * @return \Gustav\Cache\Configuration
     *   This object
     */
    public function setLogConfiguration(LogConfiguration $configuration): self
    {
        $this->_logConfiguration = $configuration;
        return $this;
    }

    /**
     * Returns the configuration of the logger to use here.
     *
     * @return \Gustav\Utils\Log\Configuration
     *   The logger's configuration
     */
    public function getLogConfiguration(): LogConfiguration
    {
        return $this->_logConfiguration;
    }
}