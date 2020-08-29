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
        $store->set('key', 'value');
        $store->set('products.item.price', 200);

        $this->assertSame('value', $store->get('key'));
        $this->assertSame(200, $store->get('products.item.price'));
    }

    /** @test */
    public function testSkipWritingToDiskIfsettingEmptyArray()
    {
        $store = new Store($this->tempFile);
        $store->set('key', 'value');
        touch($this->tempFile, 101);

        $store->set([]);

        $this->assertEquals(filemtime($this->tempFile), 101);
    }

    /** @test */
    public function testPushAValueToANonExistingKey()
    {
        $store = new Store($this->tempFile);
        $store->push('key', 'value');

        $this->assertSame(['value'], $store->get('key'));
    }

    /** @test */
    public function testPushAValueToAnExistingKey()
    {
        $store = new Store($this->tempFile);
        $store->set('key', 'value');
        $store->push('key', 'value2');

        $this->assertSame(['value', 'value2'], $store->get('key'));
    }

    /** @test */
    public function testPrependAValueToANonExistingKey()
    {
        $store = new Store($this->tempFile);
        $store->prepend('key', 'value');

        $this->assertSame(['value'], $store->get('key'));
    }

    /** @test */
    public function testPrependAValueToAnExistingKey()
    {
        $store = new Store($this->tempFile);
        $store->prepend('key', 'value');
        $store->prepend('key', 'value2');

        $this->assertSame(['value2', 'value'], $store->get('key'));
    }

    /** @test */
    public function testDetermineIfTheStoreHoldsAValueForAGivenName()
    {
        $store = new Store($this->tempFile);
        $this->assertFalse($store->has('key'));

        $store->set('key', 'value');
        $this->assertTrue($store->has('key'));
    }

    /** @test */
    public function testWllReturnDefaultValueWhenUsingANonExistingKey()
    {
        $store = new Store($this->tempFile);
        $this->assertSame('default', $store->get('key', 'default'));
    }

    public function testWilReturnNullForNonExistingValue()
    {
        $store = new Store($this->tempFile);
        $this->assertNull($store->get('non existing key'));
    }

    public function testStoreAnInteger()
    {
        $store = new Store($this->tempFile);
        $store->set('number', 1);

        $this->assertSame(1, $store->get('number'));
    }

    public function testOverwriteAValue()
    {
        $store = new Store($this->tempFile);
        $store->set('key', 'value');

        $store->set('key', 'otherValue');

        $this->assertSame('otherValue', $store->get('key'));
    }

    public function testFetchAllValuesAtOnce()
    {
        $store = new Store($this->tempFile);
        $store->set('key', 'value');
        $store->set('otherKey', 'otherValue');

        $this->assertSame([
            'key' => 'value',
            'otherKey' => 'otherValue',
        ], $store->all());
    }

    public function testStoreMultipleKeyValuePairsInOneGo()
    {
        $values = [
            'key' => 'value',
            'otherKey' => 'otherValue',
        ];

        $store = new Store($this->tempFile);
        $store->set($values);

        $this->assertSame('value', $store->get('key'));
        $this->assertSame($values, $store->all());
    }

    public function testStoreValuesWithoutForgettingTheOldValues()
    {
        $store = new Store($this->tempFile);
        $store->set('test1', 'value1');
        $store->set('test2', 'value2');

        $this->assertSame([
            'test1' => 'value1',
            'test2' => 'value2',
        ], $store->all());

        $store->set(['test3' => 'value3']);

        $this->assertSame([
            'test1' => 'value1',
            'test2' => 'value2',
            'test3' => 'value3',
        ], $store->all());
    }

    /** @test */
    public function testForgetAValue()
    {
        $store = new Store($this->tempFile);
        $store->set('key', 'value');
        $store->set('otherKey', 'otherValue');
        $store->set('otherKey2', 'otherValue2');
        $store->forget('otherKey');

        $this->assertSame('value', $store->get('key'));
        $this->assertNull($store->get('otherKey'));
        $this->assertSame('otherValue2', $store->get('otherKey2'));
    }

    /** @test */
    public function testImplementsArrayAccess()
    {
        $store = new Store($this->tempFile);

        $this->assertEmpty($store['key']);

        $store['key'] = 'value';

        $this->assertNotEmpty($store['key']);
        $this->assertSame('value', $store['key']);

        unset($store['key']);

        $this->assertEmpty($store['key']);
        $this->assertNull($store['key']);
        $this->assertFalse(isset($store['key']));

        $store['key'] = 'value';

        $this->assertTrue(isset($store['key']));
    }

    /** @test */
    public function testImplementsCountable()
    {
        $store = new Store($this->tempFile);

        $this->assertCount(0, $store);

        $store->set('key', 'value');

        $this->assertCount(1, $store);
    }

    /** @test */
    public function testDeleteUnderlyingFileIfNoValuesAreLeftInIt()
    {
        $store = new Store($this->tempFile);

        $this->assertFileDoesNotExist($this->tempFile);

        $store->set('key', 'value');

        $this->assertFileExists($this->tempFile);

        $store->forget('key');

        $this->assertFileDoesNotExist($this->tempFile);

        $store->set('key', 'value');
        $store->flush();

        $this->assertFileDoesNotExist($this->tempFile);
    }

    /** @test */
    public function testAllMethodWillAlwaysReturnAnArray()
    {
        $store = new Store($this->tempFile);

        $this->assertFileDoesNotExist($this->tempFile);

        touch($this->tempFile);

        $this->assertStringEqualsFile($this->tempFile, '');
        $this->assertIsArray($store->all());

        $store->flush();

        $this->assertFileDoesNotExist($this->tempFile);
    }
}
