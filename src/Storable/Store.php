<?php

namespace Storable;

class Store
{
    /**
     * All key value pairs stored in settings repository.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new store repository instance.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get the items of the project.
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }
}
