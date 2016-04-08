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

/**
 * This is a common interface for testing cache item pools and cache items.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
abstract class ACacheItemPoolTest extends ATestCase
{
    /**
     * The configuration data to use here.
     *
     * @var \Gustav\Cache\Configuration
     */
    protected $_configuration;

    /**
     * The cache ite pool to test here.
     *
     * @var \Gustav\Cache\Filesystem\CacheItemPool
     */
    protected $_pool;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $manager = $this->_initialize();

        $func = function() {
            for($i = 0; $i < 10; $i++) {
                yield "test{$i}" => $i;
            }
        };
        $this->_pool = $manager->getItemPool("test", $func);
    }

    /**
     * Tests to return a single cache item.
     *
     * @test
     */
    public function testGetItem()
    {
        $item = $this->_pool->getItem("test1");
        $this->assertTrue($item->isHit());
        $this->assertEquals(1, $item->get());
    }
    
    /**
     * Tests to return multiple cache items.
     *
     * @test
     */
    public function testGetItems()
    {
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
     * Tests the existence of items.
     *
     * @test
     */
    public function testHasItem()
    {
        $this->assertTrue($this->_pool->hasItem("test1"));
        $this->assertFalse($this->_pool->hasItem("foo"));
    }

    /**
     * Tests clearing of cache pool.
     *
     * @test
     */
    public function testClear()
    {
        $this->assertTrue($this->_pool->clear());
        for($i = 0; $i < 10; $i++) {
            $this->assertFalse($this->_pool->hasItem("test{$i}"));
        }
    }

    /**
     * Tests the deletion of single cache items.
     *
     * @test
     */
    public function testDeleteItem()
    {
        $this->assertTrue($this->_pool->hasItem("test1"));
        $this->assertTrue($this->_pool->deleteItem("test1"));
        $this->assertFalse($this->_pool->hasItem("test1"));
        $this->assertTrue($this->_pool->hasItem("test2"));
    }
    
    /**
     * Tests the deletion of multiple cache items.
     */
    public function testDeleteItems()
    {
        $keys = ["test1", "test2", "foo"];
        $this->assertTrue($this->_pool->deleteItems($keys));
        
        foreach($keys as $key) {
            $this->assertFalse($this->_pool->hasItem($key));
        }
    }
    
    
    /**
     * Tests to add a new cache item.
     *
     * @test
     */
    public function testSaveItem()
    {
        $item = $this->_pool->getItem("foo");
        $this->assertFalse($item->isHit());
        
        $item->set("bar");
        $this->assertEquals("bar", $item->get());
        $this->assertTrue($this->_pool->save($item));
        
        $item2 = $this->_pool->getItem("foo");
        $this->assertTrue($item2->isHit());\print_r($item2);
        $this->assertEquals("bar", $item2->get());
    }
    
    
    /**
     * Tests to save and commit an item deferred.
     *
     * @test
     */
    public function testSaveDeferred()
    {
        $item = $this->_pool->getItem("foo");
        $this->assertFalse($item->isHit());
        $item->set("bar");
        $this->assertTrue($this->_pool->saveDeferred($item));
        
        $this->assertTrue($this->_pool->getItem("foo")->isHit());
        
        $this->assertTrue($this->_pool->commit());
        $this->assertTrue($this->_pool->getItem("foo")->isHit());
    }
    
    /**
     * Tests to expire an item with CacheItem::expiresAt().
     * 
     * @test
     */
    public function testExpiresAt()
    {
        $item = $this->_pool->getItem("test1");
        $this->assertTrue($item->isHit());
        $item->expiresAt(new \DateTime("now + 1 seconds"));
        $this->_pool->save($item);
        
        \sleep(2);
        
        $this->assertFalse($item->isHit());
        $this->assertFalse($this->_pool->hasItem("test1"));
    }

    /**
     * Tests to expire an item with CacheItem::expiresAfter().
     *
     * @test
     */
    public function testExpiresAfter()
    {
        $item = $this->_pool->getItem("test1");
        $this->assertTrue($item->isHit());
        $item->expiresAfter(1);
        $this->_pool->save($item);

        \sleep(2);

        $this->assertFalse($item->isHit());
        $this->assertFalse($this->_pool->hasItem("test1"));
    }

    /**
     * Initializes the configuration and cache manager
     *
     * @return \Gustav\Cache\ACacheManager
     *   The cache manager to use here
     */
    abstract protected function _initialize();
}