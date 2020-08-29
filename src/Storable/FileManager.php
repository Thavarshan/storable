<?php

namespace Storable;

use Storable\Contracts\FileManagerInterface;

class FileManager implements FileManagerInterface
{
    /**
     * Full/absolute path to file being manipulated.
     *
     * @var string
     */
    protected $file;

    /**
     * Create new instance of file manipulator.
     *
     * @param string $file
     *
     * @return void
     */
    public function __construct(string $file)
    {
        $this->setPath($file);
    }

    /**
     * Set the file location attribute.
     *
     * @param string $file
     */
    public function setPath(string $file): void
    {
        if ($file[0] != '/') {
            $file = DOCROOT . $file;
        }

        $this->file = $file;
    }

    /**
     * read in and return the current content of the file.
     *
     * @return string|bool
     */
    public function read()
    {
        return \file_get_contents($this->file, true);
    }

    /**
     * Write given contents to saved file.
     *
     * @param string $content
     * @param int    $flag
     *
     * @return bool
     */
    public function write(string $content, ?int $flag = null): bool
    {
        return (bool) \file_put_contents($this->file, $content, $flag);
    }
}
