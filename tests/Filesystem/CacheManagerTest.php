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
use Gustav\Cache\Filesystem\CacheItemPool;
use Gustav\Cache\Filesystem\CacheManager;
use Gustav\Cache\Tests\ACacheManagerTest;

/**
 * This class is used for testing the cache manager with cache pools on file
 * system.
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
    protected function _initialize(): void
    {
        $this->_configuration = new Configuration();
        $this->_configuration->setImplementation(CacheManager::class)
            ->setDirectory(\dirname(\dirname(__DIR__)) . "/data/");
    }
    
    /**
     * @inheritdoc
     */
    public function tearDown(): void
    {
        if(\file_exists($this->_configuration->getDirectory() . "test")) {
            \unlink($this->_configuration->getDirectory() . "test");
        }
    }

    /**
     * Tests the return types of the methods.
     *
     * @test
     */
    public function testReturnTypes(): void
    {
        $manager = ACacheManager::getInstance($this->_configuration);
        $this->assertTrue($manager instanceof CacheManager);

        $pool1 = $manager->getItemPool("test");
        $this->assertTrue($pool1 instanceof CacheItemPool);
    }

    /**
     * @inheritdoc
     */
    public function testCreator(): void
    {
        parent::testCreator();
        $this->assertFileExists($this->_configuration->getDirectory() . "test");
    }
}