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

namespace Gustav\Cache\Debug;

use \Gustav\Cache\CacheException,
    \Gustav\Cache\Configuration,
    \Gustav\Cache\ICache,
    \Gustav\Utils\ErrorHandler;

/**
 * This class implements the cache-interface. This cache will not store any
 * data. The purpose of this implementation is debugging of the software that
 * uses this cache.
 *
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Cache.Debug
 * @since   1.0
 */
class Cache implements ICache {
    /**
     * This is an array that contains all opened cache files.
     *
     * @var    \Gustav\Cache\Debug\Cache[]
     * @static
     */
    private static $_openedFiles = array();

    /**
     * This is an array that contains all locked cache files.
     *
     * @var    \Gustav\Cache\Debug\Cache[]
     * @static
     */
    private static $_lockedFiles = array();

    /**
     * This is the name of the current opened cache file.
     *
     * @var string
     */
    private $_fileName;

    /**
     * The full file system path to the cache file.
     *
     * @var string
     */
    private $_filePath;

    /**
     * This array contains all the saved data in this cache file.
     *
     * @var array
     */
    private $_data;

    /**
     * This attribute indicates whether the cache file is deleted (true) or not
     * (false).
     *
     * @var boolean
     */
    private $_deleted = false;

    /**
     * The constructor of this class. This constructor is private. To open a
     * cache file use \Gustav\Cache\ICache::openFile().
     *
     * @param string $fileName The file-name
     * @param string $filePath The full path to the cache file
     * @param array  $data     The data
     */
    private function __construct($fileName, $filePath, array $data) {
        $this->_fileName = (string) $fileName;
        $this->_filePath = (string) $filePath;
        $this->_data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public static function openFile($fileName, Configuration $config,
            callable $creator = null) {
        $fileName = (string) $fileName;
        $filePath = $config->getDirectory() . $fileName;
        $data = array();

        //already opened?
        if(isset(self::$_openedFiles[$filePath])) {
            if(isset(self::$_lockedFiles[$filePath])) {
                throw CacheException::fileLocked($fileName);
            }
            return self::$_openedFiles[$filePath];
        }

        if(\mb_strpos($fileName, "..") !== false ||
            \mb_strpos($fileName, "/") !== false) {
            throw CacheException::badFileName($fileName);
        }

        //create a new file
        if($creator !== null) {
            $data = \call_user_func($creator);
        }
        self::$_openedFiles[$filePath] = new self($fileName, $filePath, $data);

        return self::$_openedFiles[$filePath];
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator() {
        return new \ArrayIterator($this->_data);
    }

    /**
     * {@inheritDoc}
     */
    public function getData($key) {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }

        $key = (string) $key;
        if(isset($this->_data[$key])) {
            return $this->_data[$key];
        }
        ErrorHandler::setWarning("cache key \"{$key}\" not found");
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function setData($key, $value, $validity = 0) {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }

        $key = (string) $key;
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function unsetData($key) {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }

        $key = (string) $key;
        if(isset($this->_data[$key])) {
            unset($this->_data[$key]);
        } else {
            ErrorHandler::setWarning("cache key \"{$key}\" not found");
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasData($key) {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }

        $key = (string) $key;
        return isset($this->_data[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function clearFile() {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }
        $this->_data = array();
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function saveFile($force = false) {
        //nothing to do here
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteFile() {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }

        $this->_deleted = true;
        unset($this->_data);
        unset(self::$_openedFiles[$this->_filePath]);
        unset(self::$_lockedFiles[$this->_filePath]);
    }

    /**
     * {@inheritDoc}
     */
    public function lockFile() {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }

        self::$_lockedFiles[$this->_filePath] = true;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function unlockFile() {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }

        if(isset(self::$_lockedFiles[$this->_filePath])) {
            unset(self::$_lockedFiles[$this->_filePath]);
        }
        return $this;
    }
}