<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

use Psr\Container\ContainerInterface;

/**
 * @template T of object
 */
interface DecoratorInterface
{
    /**
     * @param ContainerInterface $container
     * @param T $service
     * @param mixed ...$args
     * @return void
     */
    public function __invoke(ContainerInterface $container, object $service, mixed ...$args): void;
}
