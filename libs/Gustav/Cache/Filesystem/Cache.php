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

namespace Gustav\Cache\Filesystem;

use \Gustav\Cache\CacheException,
    \Gustav\Cache\CacheManager,
    \Gustav\Cache\Configuration,
    \Gustav\Cache\ICache,
    \Gustav\Utils\ErrorHandler;

/**
 * This class implements the cache-interface. All the data is saved serialized
 * on file-system.
 * 
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Cache.Filesystem
 * @since   1.0
 */
class Cache implements ICache {
    /**
     * This is an array that contains all opened cache files.
     *
     * @var       \Gustav\Cache\Filesystem\Cache[]
     * @staticvar
     */
    private static $_openedFiles = array();
    
    /**
     * This is an array that contains all locked cache files.
     *
     * @var       \Gustav\Cache\Filesystem\Cache[]
     * @staticvar
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
     * The configuration of this cache file.
     *
     * @var \Gustav\Cache\Configuration
     */
    private $_config;
    
    /**
     * This attribute indicates whether the cache file is deleted (true) or not
     * (false).
     *
     * @var boolean
     */
    private $_deleted = false;
    
    /**
     * This field indicates whether this cache file has changed on runtime
     * (true) or not (false). If this is false \Gustav\Cache\ICache::saveFile()
     * doesn't do anything.
     *  
     * @var boolean
     */
    private $_updated = false;
    
    /**
     * The constructor of this class. This constructor is private. To open a
     * cache file use \Gustav\Cache\ICache::openFile().
     *
     * @param string                      $fileName The file-name
     * @param string                      $filePath The full path to the cache
     *                                              file
     * @param array                       $data     The data
     * @param \Gustav\Cache\Configuration $config   Some configurations
     */
    private function __construct($fileName, $filePath, array $data,
            Configuration $config) {
        $this->_fileName = (string) $fileName;
        $this->_filePath = (string) $filePath;
        $this->_data = $data;
        $this->_config = $config;
    }
    
    /**
     * @see \Gustav\Cache\ICache::openFile()
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
        
        //try to load from file system
        if(!\file_exists($config->getDirectory())) {
            \mkdir($config->getDirectory());
        }
        
        if(\mb_strpos($fileName, "..") !== false ||
                \mb_strpos($fileName, "/") !== false) {
            throw CacheException::badFileName($fileName);
        }
        if(\file_exists($filePath)) {
            $contents = \file_get_contents($filePath);
            if($contents === false) {
                if($creator !== null) { //try to generate the data automatically
                    $data = \call_user_func($creator);
                    ErrorHandler::setWarning("cannot read cache file");
                } else {
                    throw CacheException::fileUnreadable($fileName);
                }
            } else {
                $data = \unserialize($contents);
            }
            self::$_openedFiles[$filePath] = new self($fileName, $filePath,
                    $data, $config);
            
            return self::$_openedFiles[$filePath];
        }
        
        //create a new file
        if($creator !== null) {
            $data = \call_user_func($creator);
        }
        self::$_openedFiles[$filePath] = new self($fileName, $filePath, $data,
                $config);
        if($data) {
            self::$_openedFiles[$filePath]->saveFile(true);
        }
        
        return self::$_openedFiles[$filePath];
    }
    
    /**
     * @see \Gustav\Cache\ICache::getData()
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
     * @see \Gustav\Cache\ICache::setData()
     */
    public function setData($key, $value) {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }
        
        $key = (string) $key;
        $this->_data[$key] = $value;
        $this->_updated = true;
    }
    
    /**
     * @see \Gustav\Cache\ICache::unsetData()
     */
    public function unsetData($key) {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }
        
        $key = (string) $key;
        if(isset($this->_data[$key])) {
            unset($this->_data[$key]);
            $this->_updated = true;
        } else {
            ErrorHandler::setWarning("cache key \"{$key}\" not found");
        }
    }
    
    /**
     * @see \Gustav\Cache\ICache::hasData()
     */
    public function hasData($key) {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }
        
        $key = (string) $key;
        return isset($this->_data[$key]);
    }
    
    /**
     * @see \Gustav\Cache\ICache::saveFile()
     */
    public function saveFile($force = false) {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }
        if($force !== true && $this->_deleted === false) { //not changed...
            return;
        }
        
        $contents = \serialize($this->_data);
        $return = \file_put_contents($this->_filePath, $contents);
        if($return === false) {
            throw CacheException::fileUnwritable($this->_filePath);
        }
        $this->_updated = false;
    }
    
    /**
     * @see \Gustav\Cache\ICache::deleteFile()
     */
    public function deleteFile() {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }
        
        $this->_deleted = true;
        unset(self::$_openedFiles[$this->_filePath]);
        unset(self::$_lockedFiles[$this->_filePath]);
        $return = \unlink($this->_filePath);
        if($return === false) {
            throw CacheException::fileUndeletable($this->_fileName);
        }
    }
    
    /**
     * @see \Gustav\Cache\ICache::lockFile()
     */
    public function lockFile() {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }
        
        self::$_lockedFiles[$this->_filePath] = true;
    }
    
    /**
     * @see \Gustav\Cache\ICache::unlockFile()
     */
    public function unlockFile() {
        if($this->_deleted === true) {
            throw CacheException::fileDeleted($this->_fileName);
        }
        
        if(isset(self::$_lockedFiles[$this->_filePath])) {
            unset(self::$_lockedFiles[$this->_filePath]);
        }
    }
    
    /**
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator() {
        return new \ArrayIterator($this->_data);
    }
}