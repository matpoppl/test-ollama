<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;

class ServiceContainer implements ContainerInterface
{
    private readonly FactoriesContainer $factories;
    private readonly DecoratorsContainer $decorators;
    /** @var array<string, string> */
    private readonly array $aliases;
    /** @var array<string, object> */
    private array $services = [];

    /**
     * @param array{
     *     aliases: array<string, string>,
     *     factories: array<string, string>,
     *     decorators: array<string, string>,
     * } $options
     */
    public function __construct(array $options)
    {
        $this->aliases = $options['aliases'] ?? [];
        $this->factories = new FactoriesContainer($options['factories'] ?? [], $this);
        $this->decorators = new DecoratorsContainer($options['decorators'] ?? [], $this);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->aliases)
            || array_key_exists($id, $this->services)
            || $this->factories->has($id);
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id)
    {
        while (array_key_exists($id, $this->aliases)) {
            $id = $this->aliases[$id];
        }

        return $this->services[$id] ??= $this->create($id);
    }

    public function set(string $id, object $service): void
    {
        $this->services[$id] = $service;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @param mixed ...$args
     * @return T
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(string $id, mixed ...$args): object
    {
        $factory = $this->factories->get($id);
        $service = $factory($this, $id, ...$args);
        $this->decorators->decorate($service, ...$this->decorators->get($id));

        return $service;
    }
}
