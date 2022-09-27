<?php

namespace Rahulstech\Blogging\Helpers;

use Iterator;

class ContextIterator implements Iterator
{
    private array $map_keys;
    private array $map_values;
    private int $count;
    private int $current;

    public function __construct(array $map)
    {
        
        $this->map_keys = array_keys($map);
        $this->map_values = array_values($map);
        $this->count = count($map);
        $this->current = 0;
    }

	/**
	 * Returns the current element.
	 *
	 * @return mixed Can return any type.
	 */
	public function current(): mixed
    {
        return $this->map_values[$this->current];
    }

	/**
	 * Returns the key of the current element.
	 *
	 * @return mixed Returns `scalar` on success, or `null` on failure.
	 */
	public function key(): string
    {
        return $this->map_keys[$this->current];
    }

	/**
	 * Move forward to next element
	 * Moves the current position to the next element.
	 */
	public function next(): void
    {
        $this->current++;
    }

	/**
	 * Rewind the Iterator to the first element
	 * Rewinds back to the first element of the Iterator.
	 */
	public function rewind(): void
    {
        $this->current = 0;
    }

	/**
	 * Checks if current position is valid
	 * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
	 *
	 * @return bool The return value will be casted to `bool` and then evaluated. Returns `true` on success or `false` on failure.
	 */
	public function valid(): bool
    {
        return $this->current < $this->count;
    }
}
