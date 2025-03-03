<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

use Psr\Container\ContainerInterface;

class InvokableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $id, mixed ...$args): object
    {
        return new $id(...$args);
    }
}
