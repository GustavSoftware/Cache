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

namespace Gustav\Cache\Tests\Filesystem;

use Gustav\Cache\ACacheManager;
use Gustav\Cache\Configuration;
use Gustav\Cache\Filesystem\CacheManager;
use Gustav\Cache\Tests\ACacheItemPoolTest;

/**
 * This class is used for testing the functionality of filesystem cache item
 * pools.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheItemPoolTest extends ACacheItemPoolTest
{
    /**
     * @inheritdoc
     */
    protected function _initialize()
    {
        $this->_configuration = new Configuration();
        $this->_configuration->setImplementation(CacheManager::class)
            ->setDirectory(\dirname(\dirname(__DIR__)) . "/data/");
        return ACacheManager::getInstance($this->_configuration);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        if(\file_exists($this->_configuration->getDirectory() . "test")) {
            \unlink($this->_configuration->getDirectory() . "test");
        }
    }
}