<?php

namespace KiriminAja\Models;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class RequestPickupDataList implements IteratorAggregate, Countable
{
    private array $packages = [];

    /**
     * Add a package to the list.
     *
     * @param PackageData $package
     */
    public function add(PackageData $package): void
    {
        $this->packages[] = $package;
    }

    /**
     * Remove a package from the list by its order_id.
     *
     * @param string $order_id
     */
    public function remove(string $order_id): void
    {
        $this->packages = array_filter($this->packages, function ($package) use ($order_id) {
            return $package->order_id !== $order_id;
        });
    }

    /**
     * Get all packages in the list.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->packages;
    }

    /**
     * Implement IteratorAggregate interface.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->packages);
    }

    /**
     * Implement Countable interface.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->packages);
    }

    /**
     * Parse all packages to an array format.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function ($package) {
            return $package->toArray();
        }, $this->packages);
    }


}
