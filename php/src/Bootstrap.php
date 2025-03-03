<?php

declare(strict_types=1);

namespace App;

use Lib\ServiceContainer\ServiceContainerFactory;
use Psr\Container\ContainerInterface;

class Bootstrap
{
    public static function create(): self
    {
        $sm = (new ServiceContainerFactory())->create(
            (new ConfigBuilder)
                ->scanPath(__DIR__ . '/../configs/*.php')
                ->build()
        );

        return new self($sm);
    }

    public function __construct(private readonly ContainerInterface $container)
    {}

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getService(string $id)
    {
        return $this->container->get($id);
    }
}
