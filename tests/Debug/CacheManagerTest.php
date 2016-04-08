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

namespace Gustav\Cache\Tests\Debug;

use Gustav\Cache\ACacheManager;
use Gustav\Cache\Configuration;
use Gustav\Cache\Debug\CacheItemPool;
use Gustav\Cache\Debug\CacheManager;
use Gustav\Cache\Tests\ACacheManagerTest;

/**
 * This class is used for testing the debugger cache manager.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheManagerTest extends ACacheManagerTest
{
    /**
     * @inheritdoc
     */
    protected function _initialize()
    {
        $this->_configuration = new Configuration();
        $this->_configuration->setImplementation(CacheManager::class);
    }

    /**
     * Tests the return types of the methods.
     *
     * @test
     */
    public function testReturnTypes()
    {
        $manager = ACacheManager::getInstance($this->_configuration);
        $this->assertTrue($manager instanceof CacheManager);

        $pool1 = $manager->getItemPool("test");
        $this->assertTrue($pool1 instanceof CacheItemPool);
    }
}