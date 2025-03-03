<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

use Psr\Container\ContainerInterface;

use function array_key_exists;

readonly final class FactoriesContainer implements ContainerInterface
{
    public function __construct(
        /**
         * @template T
         * @var array<class-string<T>, class-string<FactoryInterface<T>>>
         */
        private array $factories,
        private ContainerInterface $container,
    ) {}

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->factories);
    }

    public function get(string $id): FactoryInterface
    {
        $factoryId = $this->factories[$id] ?? AutowireFactory::class;

        if ($this->container->has($factoryId)) {
            return $this->container->get($factoryId);
        }

        return new $factoryId();
    }
}
