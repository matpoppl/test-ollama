<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

use ArrayAccess;

class ServicesConfigs implements ArrayAccess
{
    public function __construct(private readonly array $configs)
    {}

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->configs);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->configs[$name] ?? $default;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \RuntimeException('Config is read-only');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \RuntimeException('Config is read-only');
    }
}
