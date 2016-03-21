<?php

/*
 * Gustav Cache - A small and simple PHP cache system.
 * Copyright (C) 2014-2016  Gustav Software
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

use \Gustav\Utils\Miscellaneous;

/**
 * This class is used for some important configurations of this cache system.
 * 
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Cache
 * @since   1.0
 */
class Configuration {
    /**
     * This is the class name of the implementation of the cache files that
     * should be used on runtime. Consider that this class has to implement the
     * ICache interface.
     *
     * @var string
     */
    private $_implementation = "\\Gustav\\Cache\\Filesystem\\Cache";
    
    /**
     * The absolute path to the directory which contains all the saved cache
     * files.
     *
     * @var string
     */
    private $_dir;
    
    /**
     * Sets the class name of the implementation of the cache files that should
     * be used on runtime. Consider that this class name has to implement the
     * ICache interface. Otherwise this method will throw an CacheException.
     *
     * @param  string                       $className The class name
     * @return \Gustav\Cache\Configuration             This object
     * @throws \Gustav\Cache\CacheException            Invalid implementation
     */
    public function setImplementation(string $className): Configuration {
        if(!Miscellaneous::implementsInterface($className,
                "\\Gustav\\Cache\\ICache")) {
            throw CacheException::invalidImplementation($className);
        }
        $this->_implementation = $className;
        return $this;
    }
    
    /**
     * Returns the class name of the implementation of the cache files that
     * should be used on runtime.
     *
     * @return string The class name
     */
    public function getImplementation(): string {
        return $this->_implementation;
    }
    
    /**
     * Sets the directory where the cache files can be saved. Consider that this
     * path should be absolute.
     *
     * @param  string                      $dir The directory
     * @return \Gustav\Cache\Configuration      This object
     */
    public function setDirectory(string $dir): Configuration {
        $this->_dir = $dir;
        return $this;
    }
    
    /**
     * Returns the directory where the cache files can be saved. This path is
     * an absolute path.
     *
     * @return string The path where the cache files can be saved
     */
    public function getDirectory(): string {
        return $this->_dir;
    }
}