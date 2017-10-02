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

namespace Gustav\Cache;

use Gustav\Utils\InvalidArgumentException;
use Psr\Cache\InvalidArgumentException as IInvalidArgumentException;

/**
 * This is an exception that occurs on handling of invalid arguments in this
 * project.
 *
 * Possible error codes are:
 * 1 - The given class name of the cache implementation was invalid.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class InvalidKeyException extends InvalidArgumentException implements
    IInvalidArgumentException
{
    /**
     * The possible error codes.
     */
    const INVALID_KEY = 2;
    
    /**
     * This method creates an exception if a key with an invalid data type was
     * given to the cache item pool.
     *
     * @param \Exception|null $previous
     *   Previous exception
     * @return \Gustav\Cache\InvalidKeyException
     *   The new exception
     */
    public static function invalidKey(\Exception $previous = null): self
    {
        return new self(
            "invalid cache item key given",
            self::INVALID_KEY,
            $previous
        );
    }
}