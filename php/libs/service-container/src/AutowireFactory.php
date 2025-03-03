<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

use Psr\Container\ContainerInterface;

class AutowireFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $id, mixed ...$args): object
    {
        $parameters = (new \ReflectionClass($id))->getConstructor()?->getParameters() ?? [];

        foreach ($parameters as $i => $parameter) {
            $typeName = $parameter->getType()?->getName();

            if ($parameter->isOptional() && (null === $typeName || ! $container->has($typeName))) {
                break;
            }

            $args[$i] = $container->get($typeName);
        }

        return new $id(...$args);
    }
}
