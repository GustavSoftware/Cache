<?php

/*
 * Gustav Cache - A small and simple PHP cache system.
 * Copyright (C) 2014-2015  Gustav Software
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
 * This class manages all the cache files and the implementations of caches.
 * 
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Cache
 * @since   1.0
 */
class CacheManager {
    /**
     * Some additional configuration of this cache manager.
     * 
     * @var \Gustav\Cache\Configuration
     */
    private $_config;
    
    /**
     * Constructor of this class. Sets the configuration of the cache system.
     * 
     * @param \Gustav\Cache\Configuration $config Some configurations
     */
    public function __construct(Configuration $config) {
        $this->_config = $config;
    }
    
    /**
     * Returns the configuration object of this cache system.
     * 
     * @return \Gustav\Cache\Configuration The configuration
     */
    public function getConfiguration() {
        return $this->_config;
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
        $impl = $this->_config->getImplementation();
        return $impl::openFile($fileName, $this->_config, $creator);
    }
}