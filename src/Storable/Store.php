<?php

namespace Storable;

use Countable;
use ArrayAccess;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Config\Repository as RepositoryContract;

class Store implements ArrayAccess, Countable, RepositoryContract
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
     * Determine if the given configuration value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->all(), $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param array|string $key
     * @param mixed        $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->all(), $key, $default);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
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
     * Set a given configuration value.
     *
     * @param array|string $key
     * @param mixed        $value
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        if ($key === []) {
            return;
        }

        $store = $this->all();

        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                Arr::set($store, $innerKey, $innerValue);
            }
        } else {
            Arr::set($store, $key, $value);
        }

        $this->setContent($store);
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = Arr::wrap($this->get($key));

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function push($key, $value)
    {
        $array = Arr::wrap($this->get($key));

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Forget a value from the store.
     *
     * @param string $key
     *
     * @return void
     */
    public function forget(string $key)
    {
        $newContent = $this->all();

        unset($newContent[$key]);

        $this->setContent($newContent);
    }

    /**
     * Flush all values from the store.
     *
     * @return $this
     */
    public function flush()
    {
        return $this->setContent([]);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }

    /**
     * Count elements.
     *
     * @return int
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * Write given content to store file.
     *
     * @param array $values
     *
     * @return \Storable\Store
     */
    protected function setContent(array $values)
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
    protected function createFileManager()
    {
        return new FileManager($this->file);
    }
}
