<?php

declare(strict_types=1);

namespace App;

use Lib\ServiceContainer\FactoryInterface;
use Lib\ServiceContainer\ServicesConfigs;
use Psr\Container\ContainerInterface;
use Redis;

use function intval;

class RedisFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $id, ...$args): object
    {
        /**
         * @var array{
         *  DB_HOST: ?string,
         *  DB_PORT: ?int,
         *  DB_USER: ?string,
         *  DB_PASS: ?string,
         * } $redisOptions
         */
        $redisOptions = current($args) ?: $container->get(ServicesConfigs::class)->get('secrets')['redis'];

        return new Redis([
            'connectTimeout' => 1.5,
            'host' => $redisOptions['DB_HOST'] ?? '127.0.0.1',
            'port' => intval($redisOptions['DB_PORT'] ?? 6379),
            'auth' => [
                $redisOptions['DB_USER'],
                $redisOptions['DB_PASS'],
            ],
        ]);
    }
}
