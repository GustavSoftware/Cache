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

namespace Gustav\Cache\Tests;

use Gustav\Cache\ACacheManager;
use Gustav\Cache\CacheException;

/**
 * This is a common interface for testing cache managers.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
abstract class ACacheManagerTest extends ATestCase
{
    /**
     * The configuration data to use here.
     *
     * @var \Gustav\Cache\Configuration
     */
    protected $_configuration;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->_initialize();
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

    /**
     * Tests the opening of a cache pool.
     *
     * @test
     */
    public function testOpening()
    {
        $manager = ACacheManager::getInstance($this->_configuration);

        $pool1 = $manager->getItemPool("test");
        $pool2 = $manager->getItemPool("test");
        $this->assertTrue($pool1 === $pool2);
    }

    /**
     * Tests throwing of exceptions on invalid cache item pool's name.
     *
     * @test
     */
    public final function testBadFileName()
    {
        $manager = ACacheManager::getInstance($this->_configuration);
        $this->expectException(CacheException::class);
        $this->expectExceptionCode(CacheException::BAD_FILE_NAME);

        $manager->getItemPool("../invalidName");
    }

    /**
     * Tests the opening of a new cache pool with a creator.
     *
     * @test
     */
    public function testCreator()
    {
        $manager = ACacheManager::getInstance($this->_configuration);

        $func = function() {
            return [
                'foo' => "bar"
            ];
        };

        $pool = $manager->getItemPool("test", $func);
        $this->assertTrue($pool->hasItem("foo"));
    }

    /**
     * Initializes the configuration.
     */
    abstract protected function _initialize();
}