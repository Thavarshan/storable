<?php

namespace Storable\Tests;

use Storable\Store;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    public function testBasic()
    {
        $Store = new Store('Example Store');

        $this->assertInstanceOf(Store::class, $Store);
        $this->assertEquals('Example Store', $Store->name());
    }
}
