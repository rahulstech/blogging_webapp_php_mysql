<?php

namespace Rahulstech\Blogging\Helpers;

use Countable;
use ArrayObject;
use Traversable;
use IteratorAggregate;
use InvalidArgumentException;

class Context implements Countable
{
    /**
     * @var array<string,mixed>
     */
    private array $map = array();

    public function __construct(array $src = array())
    {
        $this->map = (new ArrayObject($src))->getArrayCopy();
    }

    public function put(string $key, mixed $value): mixed
    {
        $map = $this->map;
        $old = $this->exists($key) ? $map[$key] : null;
        $this->map[$key] = $value;
        return $old;
    }

    public function get(string $key, mixed $default=null): mixed 
    {
        if ($this->exists($key)) return $this->map[$key];
        return $default;
    }

    public function exists(string $key): bool 
    {
        return array_key_exists($key,$this->map);
    }

    public function count(): int 
    {
        return count($this->map);
    }

    public function remove(string $key): mixed
    {
        if ($this->exists($key))
        {
            $old = $this->get($key);
            unset($this->map[$key]);
            return $old;
        }
        return null;
    }

    public function merge(array|Context $another): void 
    {
        if ($another instanceof Context)
        {
            $this->merge($another->map);
        }
        else
        {
            $this->map = array_merge($this->map,$another);
        }
    }

    public function clear(): void 
    {
        $this->map = array();
    }

    /**
     * @return string[]
     */
    public function keys(): array 
    {
        return array_keys($this->map);
    }

    /**
     * @return mixed[]
     */
    public function values(): array 
    {
        return array_values($this->map);
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array 
    {
        $copy = new ArrayObject($this->map);
        return $copy->getArrayCopy();
    }
}
