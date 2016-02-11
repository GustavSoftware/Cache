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

/**
 * This is an interface for saving data that are used frequently and should not
 * be loaded from database every time.
 * 
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Cache
 * @since   1.0
 */
interface ICache extends \IteratorAggregate {
    /**
     * This method is used to open a cache-file for access on the saved data.
     * If the given file does not exists, an new file will be generated with the
     * contents generated by the additional creation function.
     *
     * @param  string                       $fileName The name of the file
     * @param  \Gustav\Cache\Configuration  $config   Some configurations
     * @param  callable                     $creator  An additional operation
     *                                                for generation of data
     * @return \Gustav\Cache\ICache                   The cache file
     * @throws \Gustav\Cache\CacheException           Reading failed or file is
     *                                                locked
     * @static
     */
    public static function openFile($fileName, Configuration $config,
            callable $creator = null);
    
    /**
     * Loads the saved value that belongs to the given key in this file.
     *
     * @param  string                       $key The key
     * @return mixed                             The value
     * @throws \Gustav\Cache\CacheException      File is deleted
     */
    public function getData($key);
    
    /**
     * Changes the value that belongs to the given key in this file. The third
     * optional argument sets how long the data will be valid (in seconds).
     * After this time period \Gustav\Cache\ICache::hasData() will return false
     * and remove this value. If this value isn't a positive integer, the data
     * will not expire.
     *
     * @param  string                       $key      The key
     * @param  mixed                        $value    The new value
     * @param  integer                      $validity The time until expiration
     * @return \Gustav\Cache\ICache                   This object
     * @throws \Gustav\Cache\CacheException           File is deleted
     */
    public function setData($key, $value, $validity = 0);
    
    /**
     * Removes the value that belongs to the given key from this file.
     *
     * @param  string                       $key The key
     * @return \Gustav\Cache\ICache              This object
     * @throws \Gustav\Cache\CacheException      File is deleted
     */
    public function unsetData($key);
    
    /**
     * Checks if the cache file contains a value that is mapped to the given
     * key.
     * 
     * @param  string                       $key The key
     * @return boolean                           true, if data exists, otherwise
     *                                           false
     * @throws \Gustav\Cache\CacheException      File is deleted
     */
    public function hasData($key);
    
    /**
     * Removes all saved data from this cache-file. In contrast to
     * \Gustav\Cache\ICache::deleteFile() the file wil not be removed from this
     * cache system. Only the content of the file will be cleared. New data can
     * be inserted afterwards.
     * 
     * @return \Gustav\Cache\ICache         This object
     * @throws \Gustav\Cache\CacheException File is deleted
     */
    public function clearFile();
    
    /**
     * Saves the cache-file. If the argument is true, the saving process will
     * be forced (even if no data has changed). Otherwise the cache-file will
     * only be saved if data was updated. This is the default behavior.
     *
     * @param  boolean                      $force true, if saving should be
     *                                             forced, otherwise false
     * @return \Gustav\Cache\ICache                This object
     * @throws \Gustav\Cache\CacheException        Writing failed or file is
     *                                             deleted or file is outdated
     */
    public function saveFile($force = false);
    
    /**
     * Deletes the cache-file. In contrast to \Gustav\Cache\ICache::clearFile()
     * this method deletes the cache file from this cache system. No data can
     * be inserted afterwards.
     *
     * @throws \Gustav\Cache\CacheException Deleting failed or file is deleted
     *                                      or file is outdated
     */
    public function deleteFile();
    
    /**
     * Sets a lock on this file. So the file can't be loaded by other
     * components until termination of this script.
     * 
     * @return \Gustav\Cache\ICache         This object
     * @throws \Gustav\Cache\CacheException File is deleted
     */
    public function lockFile();
    
    /**
     * Removes the lock of this file.
     * 
     * @return \Gustav\Cache\ICache         This object
     * @throws \Gustav\Cache\CacheException File is deleted
     */
    public function unlockFile();
}