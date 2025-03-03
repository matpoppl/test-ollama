<?php

declare(strict_types=1);

namespace App;

use Lib\ServiceContainer\FactoryInterface;
use Psr\Container\ContainerInterface;

class PGVectorPDORepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $id, ...$args): object
    {
        return new $id($container->get(PGVectorPDO::class));
    }
}
