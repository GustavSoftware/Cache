<?php

/*
 * Gustav ORM - A simple PHP framework for object-relational mappings.
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

namespace Gustav\Cache\Filesystem;

/**
 * This class is used for iteration on cache data.
 *
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Orm.Filesystem
 * @since   1.0
 */
class CacheIterator implements \Iterator {
    /**
     * This array contains all the saved data in this cache file. Each array
     * element is an array of two elements:
     * - 'expires' - The UNIX timestamp of the expiration date of the cache data
     *               (or 0, if the data does not expire)
     * - 'value' - The cached value
     *
     * @var array
     */
    private $_data;

    /**
     * The constructor of this class.
     *
     * @param array $data The data
     */
    public function __construct(array $data) {
        $this->_data = $data;
        \reset($this->_data);
        while(\key($this->_data) !== null &&
                !$this->_isValid(\key($this->_data))) {
            \next($this->_data);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function current() {
        return \current($this->_data)['value'];
    }

    /**
     * {@inheritDoc}
     */
    public function next() {
        do {
            \next($this->_data);
        } while(\key($this->_data) !== null &&
                !$this->_isValid(\key($this->_data)));
    }

    /**
     * {@inheritDoc}
     */
    public function key() {
        return \key($this->_data);
    }

    /**
     * {@inheritDoc}
     */
    public function valid() {
        return (\key($this->_data) !== null);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind() {
        \reset($this->_data);
        while(\key($this->_data) !== null &&
                !$this->_isValid(\key($this->_data))) {
            \next($this->_data);
        }
    }

    /**
     * Checks whether the given data with the given key is valid.
     *
     * @param  string  $key The key
     * @return boolean      true, if the data is valid, otherwise false
     */
    private function _isValid($key) {
        return $this->_data[$key]['expires'] <= 0 ||
        $this->_data[$key]['expires'] > \time();
    }
}