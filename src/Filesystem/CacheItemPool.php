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

namespace Gustav\Cache\Filesystem;

use Gustav\Cache\ACacheItemPool;
use Gustav\Cache\Configuration;

/**
 * This class represents cache pools on filesystem.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheItemPool extends ACacheItemPool
{
    /**
     * The full file system path to the cache file.
     *
     * @var string
     */
    private string $_filePath;

    /**
     * The timestamp of the last update of the cache file. This is needed to
     * avoid some problems with concurrency.
     *
     * @var int
     */
    private int $_lastUpdate;

    /**
     * Constructor of this class.
     *
     * @param string $filePath
     *   The full path to the cache item pool's file
     * @param integer $lastUpdate
     *   The timestamp of last update of the cache item pool's file
     * @param array $data
     *   The data
     * @param \Gustav\Cache\Configuration $configuration
     *   Some configurations
     */
    public function __construct(string $filePath, int $lastUpdate, array $data, Configuration $configuration)
    {
        $this->_filePath = $filePath;
        $this->_lastUpdate = $lastUpdate;
        parent::__construct($data, $configuration);
    }
    
    /**
     * @inheritdoc
     */
    protected function _persist(): bool
    {
        if(\file_exists($this->_filePath) && \filemtime($this->_filePath) > $this->_lastUpdate) {
            return false;
        }

        $contents = \serialize($this->_data);
        if(\file_put_contents($this->_filePath, $contents) !== false) {
            $this->_lastUpdate = \time();
            return true;
        }
        return false;
    }
    
    /**
     * Checks whether the file is empty and deletes it in this case.
     */
    public function __destruct()
    {
        if(empty($this->_data) && \file_exists($this->_filePath) && \filemtime($this->_filePath) <= $this->_lastUpdate) {
            \unlink($this->_filePath);
        }
    }
}