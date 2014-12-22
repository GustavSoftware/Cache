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

use \Gustav\Utils\GustavException;

/**
 * This is an exception that occurs while operating on cache-files.
 * 
 * Possible error codes are:
 * 1 - The given class name of the cache implementation was invalid.
 * 2 - The file-name contains invalid symbols.
 * 3 - Tried to access a locked file.
 * 4 - Tried to access a deleted file.
 * 5 - File can't be read.
 * 6 - File can't be written.
 * 7 - File can't be deleted.
 * 
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Cache
 * @since   1.0
 */
class CacheException extends GustavException {
    /**
     * The possible error codes.
     */
    const INVALID_IMPLEMENTATION = 1;
    const BAD_FILE_NAME = 2;
    const FILE_LOCKED = 3;
    const FILE_DELETED = 4;
    const FILE_UNREADABLE = 5;
    const FILE_UNWRITABLE = 6;
    const FILE_UNDELETABLE = 7;
    
    /**
     * This method creates an exception if the given class name of the cache
     * implementation to use doesn't implement ICache interface.
     *
     * @param  string                       $className The class name
     * @param  \Exception                   $previous  Previous exception
     * @return \Gustav\Cache\CacheException            The new exception
     * @static
     */
    public static function invalidImplementation($className,
            \Exception $previous = null) {
        $className = (string) $className;
        return new self("invalid class name: {$className}",
                self::INVALID_IMPLEMENTATION, $previous);
    }
    
    /**
     * This method creates an exception if the given file-name contains
     * invalid symbols.
     *
     * @param  string                       $fileName The file-name
     * @param  \Exception                   $previous Previous exception
     * @return \Gustav\Cache\CacheException           The new exception
     * @static
     */
    public static function badFileName($fileName, \Exception $previous = null) {
        $fileName = (string) $fileName;
        return new self("bad file name: {$fileName}", self::BAD_FILE_NAME,
                $previous);
    }
    
    /**
     * This method creates an exception if someone tried to access a locked
     * file.
     *
     * @param  string                       $fileName The file-name
     * @param  \Exception                   $previous Previous exception
     * @return \Gustav\Cache\CacheException           The new exception
     * @static
     */
    public static function fileLocked($fileName, \Exception $previous = null) {
        $fileName = (string) $fileName;
        return new self("file locked: {$fileName}", self::FILE_LOCKED,
                $previous);
    }
    
    /**
     * This method creates an exception if someone tried to access a deleted
     * file.
     *
     * @param  string                       $fileName The file-name
     * @param  \Exception                   $previous Previous exception
     * @return \Gustav\Cache\CacheException           The new exception
     * @static
     */
    public static function fileDeleted($fileName, \Exception $previous = null) {
        $fileName = (string) $fileName;
        return new self("file already deleted: {$fileName}", self::FILE_DELETED,
                $previous);
    }
    
    /**
     * This method creates an exception if reading of this file failed.
     *
     * @param  string                       $fileName The file-name
     * @param  \Exception                   $previous Previous exception
     * @return \Gustav\Cache\CacheException           The new exception
     * @static
     */
    public static function fileUnreadable($fileName,
            \Exception $previous = null) {
        $fileName = (string) $fileName;
        return new self("cannot read file: {$fileName}", self::FILE_UNREADABLE,
                $previous);
    }
    
    /**
     * This method creates an exception if writing of this file failed.
     *
     * @param  string                       $fileName The file-name
     * @param  \Exception                   $previous Previous exception
     * @return \Gustav\Cache\CacheException           The new exception
     * @static
     */
    public static function fileUnwritable($fileName,
            \Exception $previous = null) {
        $fileName = (string) $fileName;
        return new self("cannot write file: {$fileName}", self::FILE_UNWRITABLE,
                $previous);
    }
    
    /**
     * This method creates an exception if deleting of this file failed.
     *
     * @param  string                       $fileName The file-name
     * @param  \Exception                   $previous Previous exception
     * @return \Gustav\Cache\CacheException           The new exception
     * @static
     */
    public static function fileUndeletable($fileName,
            \Exception $previous = null) {
        $fileName = (string) $fileName;
        return new self("cannot delete file: {$fileName}",
                self::FILE_UNDELETABLE, $previous);
    }
}