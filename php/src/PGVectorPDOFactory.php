<?php

declare(strict_types=1);

namespace App;

use Lib\ServiceContainer\FactoryInterface;
use Lib\ServiceContainer\ServicesConfigs;
use PDO;

use Psr\Container\ContainerInterface;

use function current;
use function http_build_query;

class PGVectorPDOFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $id, ...$args): PDO
    {
        /**
         * @var array{
         *  DB_DBNAME: string,
         *  DB_HOST: ?string,
         *  DB_PORT: ?int,
         *  DB_USER: ?string,
         *  DB_PASS: ?string,
         * } $secret
         */
        $secret = current($args) ?: $container->get(ServicesConfigs::class)->get('secrets')['pgvector'];
        $authData = [
            'host' => $secret['DB_HOST'] ?? 'localhost',
            'port' => $secret['DB_PORT'] ?? 5432,
            'dbname' => $secret['DB_DBNAME'] ?? throw new \BadMethodCallException('Database name is invalid'),
        ];
        $dsn = 'pgsql:' . http_build_query($authData, '', ';');

        return new $id($dsn, $secret['DB_USER'] ?? null, $secret['DB_PASS'] ?? null);
    }
}
