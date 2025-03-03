<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

use Psr\Container\ContainerInterface;

use function array_map;
use function array_slice;

readonly final class DecoratorsContainer
{
    public function __construct(
        /**
         * @template T
         * @var array<class-string<T>, class-string<callable(T): void>|callable>
         */
        private array $decorators,
        private ContainerInterface $container,
    ) {}

    /**
     * @param string $id
     * @return callable[]
     */
    public function get(string $id): array
    {
        $decorators = $this->decorators[$id] ?? null;

        if (null === $decorators) {
            return [];
        }

        return array_map(
            fn (string|callable $decorator): callable
                => is_string($decorator)
                    ? $this->container->get($decorator)(...)
                    : $decorator,
            $decorators
        );
    }

    public function decorate(object $service, callable ...$decorators): void
    {
        foreach ($decorators as $decorator) {
            $parameters = array_slice((new \ReflectionFunction($decorator))->getParameters(), 1);

            $args = array_map(
                fn(\ReflectionParameter $param) => $this->container->get($param->getType()->getName()),
                $parameters
            );

            $decorator($service, ...$args);
        }
    }
}
