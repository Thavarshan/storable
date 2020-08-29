<?php

namespace Storable\Tests;

use PHPUnit\Framework\TestCase;
use Storable\Contracts\FileManagerInterface;

class FileManagerTest extends TestCase
{
    use Concerns\InteractsWithFile;

    public function testIsInstantiable()
    {
        $file = $this->getFileManager();

        $this->assertInstanceOf(FileManagerInterface::class, $file);
    }

    public function testReadAndWriteToFile()
    {
        $file = $this->getFileManager();

        $file->write('hello');

        $this->assertEquals('hello', $file->read());
    }
}
