<?php

declare(strict_types=1);

namespace App;

use BadMethodCallException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Lib\ServiceContainer\FactoryInterface;
use Lib\ServiceContainer\ServicesConfigs;
use Psr\Container\ContainerInterface;

use function current;

class OllamaAPIFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $id, ...$args): object
    {
        /**
         * @var array{
         *  API_BASEURL: string,
         * } $secret
         */
        $secret = current($args)
            ?: $container->get(ServicesConfigs::class)->get('secrets')['ollama']
            ?? throw new BadMethodCallException("`ollama` secret required");

        return new $id(
            httpClient: new Client([
                'base_uri' => $secret['API_BASEURL'] ?? 'http://localhost:11434/api/',
            ]),
            httpRequestFactory: new HttpFactory(),
        );
    }
}
