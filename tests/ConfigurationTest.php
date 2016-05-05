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

namespace Gustav\Cache\Tests;

use Gustav\Cache\CacheException;
use Gustav\Cache\Configuration;
use Gustav\Cache\Debug\CacheManager as DebugManager;
use Gustav\Cache\Filesystem\CacheManager as FilesystemManager;

/**
 * This class is used for testing the configuration data.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class ConfigurationTest extends ATestCase
{
    /**
     * Tests whether we can set the filesystem cache in configuration file.
     *
     * @test
     */
    public function testFilesystem()
    {
        $config = new Configuration();
        $config->setImplementation(FilesystemManager::class);
        $this->assertEquals(
            $config->getImplementation(),
            FilesystemManager::class
        );
    }

    /**
     * Tests whether we can set the debug cache in configuration file.
     *
     * @test
     */
    public function testDebug()
    {
        $config = new Configuration();
        $config->setImplementation(DebugManager::class);
        $this->assertEquals(
            $config->getImplementation(),
            DebugManager::class
        );
    }

    /**
     * Tests whether configuration file throws an exception on invalid input.
     *
     * @test
     */
    public function testException()
    {
        $config = new Configuration();
        $this->expectException(CacheException::class);
        $this->expectExceptionCode(CacheException::INVALID_IMPLEMENTATION);

        $config->setImplementation(self::class);
    }
}