<?php

/*
 * Gustav Cache - A small and simple PHP cache system.
 * Copyright (C) 2014  Chris Köcher
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

use \Gustav\Utils\Miscellaneous,
    \Gustav\Utils\TSingleton;

/**
 * This class manages all the cache files and the implementations of caches.
 * 
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Cache
 * @since   1.0
 */
class CacheManager {
    /**
     * Use the singleton design pattern.
     */
    use TSingleton;
    
    /**
     * The directory which can contain all the saved cache files. This path is
     * relative to this projects root directory. Consider, that this constant
     * is internal and should not be used in any cache implementation. Please
     * use the method \Gustav\Cache\CacheManager::getDirectory() instead.
     * 
     * @var string
     */
    const _DIR = "data/";
    
    /**
     * This is the class name of the implementation of the cache files that
     * should be used on runtime. Consider that this class has to implement the
     * ICache interface.
     * 
     * @var string
     */
    private $_implementation = "\Gustav\Cache\Filesystem\Cache";
    
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
     * @return \Gustav\Cache\CacheManager              This object
     * @throws \Gustav\Cache\CacheException            Invalid implementation
     */
    public function setImplementation($className) {
        $className = (string) $className;
        if(!Miscellaneous::implementsInterface($className,
                "\Gustav\Cache\ICache")) {
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
    public function getImplementation() {
        return $this->_implementation;
    }
    
    /**
     * Sets the directory where the cache files can be saved. Consider that this
     * path should be absolute.
     * 
     * @param  string                     $dir The directory
     * @return \Gustav\Cache\CacheManager      This object
     */
    public function setDirectory($dir) {
        $this->_dir = (string) $dir;
        return $this;
    }
    
    /**
     * Returns the directory where the cache files can be saved. This path is
     * an absolute path.
     * 
     * @return string The path where the cache files can be saved
     */
    public function getDirectory() {
        if($this->_dir === null) {
            $this->_dir = \dirname(\dirname(\dirname(__DIR__))) . "/"
                    . self::_DIR;
        }
        return $this->_dir;
    }
    
    /**
     * This method creates an object for managment of an cache-file.
     *
     * @param  string               $fileName The name of the cache-file
     * @param  callable             $creator  An additional operation for
     *                                        creation of the cache-file
     * @return \Gustav\Cache\ICache           The cache-file
     * @static
     */
    public function getCache($fileName, callable $creator = null) {
        $impl = $this->getImplementation();
        return $impl::openFile($fileName, $creator);
    }
}