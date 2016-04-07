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

namespace Gustav\Cache;

use Gustav\Utils\GustavException;
use Psr\Cache\CacheException as ICacheException;

/**
 * This is an exception that occurs while operating on cache-files.
 * 
 * Possible error codes are:
 * 1 - The given class name of the cache implementation was invalid.
 * 2 - The file-name contains invalid symbols.
 * 3 - File can't be read.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheException extends GustavException implements ICacheException
{
    /**
     * The possible error codes.
     */
    const INVALID_IMPLEMENTATION = 1;
    const BAD_FILE_NAME = 2;
    const FILE_UNREADABLE = 3;
    
    /**
     * This method creates an exception if the given class name of the cache
     * implementation to use does not implement \Gustav\Cache\ACacheManager.
     *
     * @param string $className
     *   The class name
     * @param \Exception|null $previous
     *   Previous exception
     * @return \Gustav\Cache\CacheException
     *   The new exception
     */
    public static function invalidImplementation(
        string $className,
        \Exception $previous = null
    ): self {
        return new self(
            "invalid class name: {$className}",
            self::INVALID_IMPLEMENTATION,
            $previous
        );
    }
    
    /**
     * This method creates an exception if the given file-name contains
     * invalid symbols.
     *
     * @param string $fileName
     *   The file-name
     * @param \Exception|null $previous
     *   Previous exception
     * @return \Gustav\Cache\CacheException
     *   The new exception
     */
    public static function badFileName(
        string $fileName, 
        \Exception $previous = null
    ): self {
        return new self(
            "bad file name: {$fileName}",
            self::BAD_FILE_NAME,
            $previous
        );
    }
    
    /**
     * This method creates an exception if reading of this file failed.
     *
     * @param string $fileName
     *   The file-name
     * @param \Exception|null $previous
     *   Previous exception
     * @return \Gustav\Cache\CacheException
     *   The new exception
     */
    public static function fileUnreadable(
        $fileName, 
        \Exception $previous = null
    ): self {
        return new self(
            "cannot read file: {$fileName}",
            self::FILE_UNREADABLE,
            $previous
        );
    }
}