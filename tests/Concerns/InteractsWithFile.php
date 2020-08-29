<?php

namespace Storable\Tests\Concerns;

use Storable\FileManager;
use Storable\Contracts\FileManagerInterface;

trait InteractsWithFile
{
    /**
     * Location of temporary file where keys, values are stored.
     *
     * @var string
     */
    protected $tempFile;

    public function setUp(): void
    {
        parent::setUp();

        $this->tempFile = __DIR__ . '/../temp/store.json';

        if (file_exists($this->tempFile)) {
            @unlink($this->tempFile);
        }
    }

    /**
     * Get instance of file manager.
     *
     * @return \Storable\Contracts\FileManagerInterface
     */
    protected function getFileManager(): FileManagerInterface
    {
        return new FileManager($this->tempFile);
    }
}
