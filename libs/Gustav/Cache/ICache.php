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

/**
 * This is an interface for saving data that are used frequently and shouldn't
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
     * If the given file doesn't exists, an new file will be generated with the
     * contents generated by the additional creation function.
     *
     * @param  string                       $fileName The name of the file
     * @param  callable                     $creator  An additional operation
     *                                                for generation of data
     * @return \Gustav\Cache\ICache                   The cache file
     * @throws \Gustav\Cache\CacheException           Reading failed or file is
     *                                                locked
     * @static
     */
    public static function openFile($fileName, callable $creator = null);
    
    /**
     * Loads the saved value that belongs to the given key in this file.
     *
     * @param  string                       $key The key
     * @return mixed                             The value
     * @throws \Gustav\Cache\CacheException      File is deleted
    */
    public function getData($key);
    
    /**
     * Changes the value that belongs to the given key in this file.
     *
     * @param  string                       $key   The key
     * @param  mixed                        $value The new value
     * @throws \Gustav\Cache\CacheException        File is deleted
    */
    public function setData($key, $value);
    
    /**
     * Removes the value that belongs to the given key from this file.
     *
     * @param  string                       $key The key
     * @throws \Gustav\Cache\CacheException      File is deleted
    */
    public function unsetData($key);
    
    /**
     * Saves the cache-file.
     *
     * @throws \Gustav\Cache\CacheException Writing failed or file is deleted
    */
    public function saveFile();
    
    /**
     * Deletes the cache-file.
     *
     * @throws \Gustav\Cache\CacheException Deleting failed or file is deleted
    */
    public function deleteFile();
    
    /**
     * Sets a lock on this file. So the file can't be loaded by other
     * components until termination of this script.
     *
     * @throws \Gustav\Cache\CacheException File is deleted
    */
    public function lockFile();
    
    /**
     * Removes the lock of this file.
     *
     * @throws \Gustav\Cache\CacheException File is deleted
    */
    public function unlockFile();
}