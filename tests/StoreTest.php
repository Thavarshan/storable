<?php

namespace Storable\Tests;

use Storable\Store;
use ReflectionClass;
use PHPUnit\Framework\TestCase;
use Storable\Contracts\FileManagerInterface;

class StoreTest extends TestCase
{
    use Concerns\InteractsWithFile;

    public function testCreateInstanceOfFileManager()
    {
        $store = new Store($this->tempFile);

        $storeReflection = new ReflectionClass($store);
        $method = $storeReflection->getMethod('createFileManager');
        $method->setAccessible(true);

        $this->assertInstanceOf(FileManagerInterface::class, $method->invoke($store));
    }

    public function testStoreAKeyValuePair()
    {
        $store = new Store($this->tempFile);
        $store->put('key', 'value');
        $store->put('products.item.price', 200);

        $this->assertSame('value', $store->get('key'));
        $this->assertSame(200, $store->get('products.item.price'));
    }
}
