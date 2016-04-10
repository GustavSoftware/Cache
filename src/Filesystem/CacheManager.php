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

namespace Gustav\Cache\Filesystem;

use Gustav\Cache\ACacheManager;
use Gustav\Cache\CacheException;
use Gustav\Utils\Log\LogManager;
use Psr\Cache\CacheItemPoolInterface;

/**
 * The manager of cache item pools that are saved on file system with the help
 * of PHP's serialization method.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheManager extends ACacheManager
{
    /**
     * The cache item pools that we've loaded in this session, yet.
     *
     * @var \Gustav\Cache\Filesystem\CacheItemPool[]
     */
    private $_pools = [];

    /**
     * @inheritdoc
     */
    public function getItemPool(
        string $fileName,
        callable $creator = null
    ): CacheItemPoolInterface {
        //already opened?
        if(isset($this->_pools[$fileName])) {
            return $this->_pools[$fileName];
        }

        //fetch the file directory
        $filePath = $this->_configuration->getDirectory() . $fileName;
        $data = [];

        //try to load from file system
        if(!\file_exists($this->_configuration->getDirectory())) {
            \mkdir($this->_configuration->getDirectory());
        }

        if(
            \mb_strpos($fileName, "..") !== false ||
            \mb_strpos($fileName, "/") !== false
        ) {
            throw CacheException::badFileName($fileName);
        }
        if(\file_exists($filePath)) {
            $lastUpdate = \filemtime($filePath);
            $contents = \file_get_contents($filePath);
            if($contents === false) {
                if($creator !== null) { //try to generate the data automatically
                    $data = $this->_createData($creator);
                    
                    //log a warning here
                    LogManager::getLogger(
                        $this->_configuration->getLogConfiguration()
                    )->warning("cannot read cache file \"{file}\"", [
                        'file' => $filePath
                    ]);
                } else {
                    throw CacheException::fileUnreadable($fileName);
                }
            } else {
                $data = \unserialize($contents);
            }
            $this->_pools[$fileName] = new CacheItemPool(
                $filePath,
                $lastUpdate,
                $data, 
                $this->_configuration
            );

            return $this->_pools[$fileName];
        }

        //create a new file
        if($creator !== null) {
            $data = $this->_createData($creator);
        }
        $this->_pools[$fileName] = new CacheItemPool(
            $filePath,
            0,
            $data,
            $this->_configuration
        );
        if($data) {
            $this->_pools[$fileName]->commit();
        }

        return $this->_pools[$fileName];
    }
}