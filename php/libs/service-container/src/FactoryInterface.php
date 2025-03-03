<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

use Psr\Container\ContainerInterface;

/**
 * @template T of object
 */
interface FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param class-string<T> $id
     * @param mixed ...$args
     * @return T
     */
    public function __invoke(ContainerInterface $container, string $id, mixed ...$args): object;
}
