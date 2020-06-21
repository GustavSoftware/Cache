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

namespace Gustav\Cache\Debug;

use Gustav\Cache\ACacheManager;
use Gustav\Cache\CacheException;
use Psr\Cache\CacheItemPoolInterface;

/**
 * The manager of cache item pools that are saved in memory, i.e. the saved data
 * will be lost in the next session. This implementation is just for testing
 * purposes. You should not use it in production environments for better
 * performance.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheManager extends ACacheManager
{
    /**
     * @var \Gustav\Cache\Debug\CacheItemPool[]
     */
    private array $_pools = [];
    
    /**
     * @inheritdoc
     */
    public function getItemPool(string $fileName, callable $creator = null): CacheItemPoolInterface
    {
        //already opened?
        if(isset($this->_pools[$fileName])) {
            return $this->_pools[$fileName];
        }
    
        if(\mb_strpos($fileName, "..") !== false || \mb_strpos($fileName, "/") !== false) {
            throw CacheException::badFileName($fileName);
        }
    
        //create a new file
        $data = [];
        if($creator !== null) {
            $data = $this->_createData($creator);
        }
        $this->_pools[$fileName] = new CacheItemPool(
            $data,
            $this->_configuration
        );
        
        return $this->_pools[$fileName];
    }
}