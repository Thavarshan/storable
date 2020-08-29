<?php

namespace Storable;

use Illuminate\Support\Arr;
use Storable\Contracts\FileManagerInterface;

class Store
{
    /**
     * Name of temporary JSON file where all key, value pairs will be stored.
     *
     * @var string
     */
    protected $file;

    /**
     * Create new store repository.
     *
     * @param string $file
     *
     * @return void
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Put a value in the store.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \Storable\Store
     */
    public function put(string $key, $value): Store
    {
        $store = $this->all();

        if (is_null(Arr::get($store, $key))) {
            Arr::set($store, $key, $value);
        }

        $this->setContent($store);

        return $this;
    }

    /**
     * Get a value from the store.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        $all = $this->all();

        return Arr::get($all, $name, $default);
    }

    /**
     * Get all values from the store.
     *
     * @return array
     */
    public function all(): array
    {
        if (!file_exists($this->file)) {
            return [];
        }

        return json_decode(
            $this->createFileManager()->read(),
            JSON_OBJECT_AS_ARRAY
        ) ?: [];
    }

    /**
     * Write given content to store file.
     *
     * @param array $values
     *
     * @return \Storable\Store
     */
    protected function setContent(array $values): Store
    {
        $this->createFileManager()->write(json_encode($values));

        if (!count($values)) {
            unlink($this->file);
        }

        return $this;
    }

    /**
     * Get instance of file manager.
     *
     * @return \Storable\Contracts\FileManagerInterfaces
     */
    protected function createFileManager(): FileManagerInterface
    {
        return new FileManager($this->file);
    }
}
