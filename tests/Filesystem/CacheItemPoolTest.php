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

namespace Gustav\Cache\Tests\Filesystem;

use Gustav\Cache\ACacheManager;
use Gustav\Cache\Configuration;
use Gustav\Cache\Filesystem\CacheManager;
use Gustav\Cache\Tests\ATestCase;

/**
 * This class is used for testing the functionality of filesystem cache item
 * pools.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class CacheItemPoolTest extends ATestCase
{
    /**
     * The configuration data to use here.
     *
     * @var \Gustav\Cache\Configuration
     */
    private $_configuration;

    /**
     * The cache ite pool to test here.
     *
     * @var \Gustav\Cache\Filesystem\CacheItemPool
     */
    private $_pool;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->_configuration = new Configuration();
        $this->_configuration->setImplementation(CacheManager::class)
            ->setDirectory(\dirname(\dirname(__DIR__)) . "/data/");
        $manager = ACacheManager::getInstance($this->_configuration);

        $func = function() {
            for($i = 0; $i < 10; $i++) {
                yield "test" . $i => $i;
            }
        };

        $this->_pool = $manager->getItemPool("test", $func);
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
     * Tests to return one or more cache items.
     *
     * @test
     */
    public function testGetItem()
    {
        $item = $this->_pool->getItem("test1");
        $this->assertTrue($item->isHit());

        $keys = ["test1", "test2", "foo"];
        $items = $this->_pool->getItems($keys);
        $counter = 0;
        foreach($items as $key => $value) {
            $counter++;
            $this->assertTrue(\in_array($key, $keys));
        }
        $this->assertEquals(\count($keys), $counter);
    }

    /**
     * Tests to add a new cache item.
     *
     * @test
     */
    public function testAddItem()
    {
        $item = $this->_pool->getItem("foo");
        $this->assertFalse($item->isHit());

        $item->set("bar");
        $this->_pool->save($item);

        $item2 = $this->_pool->getItem("foo");
        $this->assertTrue($item2->isHit());
    }
    
    //TODO!!!
}