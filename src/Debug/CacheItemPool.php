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

namespace Gustav\Cache\Debug;

use Gustav\Cache\ACacheItemPool;
use Gustav\Cache\Configuration;

/**
 * This class represents cache pools in memory.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheItemPool extends ACacheItemPool
{
    /**
     * Constructor of this class.
     *
     * @param array $data
     *   The data
     * @param \Gustav\Cache\Configuration $configuration
     *   Some configurations
     */
    public function __construct(
        array $data,
        Configuration $configuration
    ) {
        parent::__construct($data, $configuration);
    }
    
    /**
     * @inheritdoc
     */
    protected function _persist(): bool
    {
        return true;
    }
}