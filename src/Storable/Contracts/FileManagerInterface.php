<?php

namespace Storable\Contracts;

interface FileManagerInterface
{
    /**
     * read in and return the current content of the file.
     *
     * @return string|bool
     */
    public function read();

    /**
     * Write given contents to saved file.
     *
     * @param string $content
     * @param int    $flag
     *
     * @return bool
     */
    public function write(string $content, ?int $flag = null): bool;
}
