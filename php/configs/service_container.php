<?php

use App\OllamaAPIFactory;
use App\PGVectorPDORepositoryFactory;
use App\PGVectorPDO;
use App\PGVectorPDOFactory;
use App\PostsRepository;
use App\RedisFactory;
use Lib\OllamaAPI\OllamaAPI;

return [
    'aliases' => [

    ],
    'factories' => [
        Redis::class => RedisFactory::class,
        PGVectorPDO::class => PGVectorPDOFactory::class,
        OllamaAPI::class => OllamaAPIFactory::class,
        PostsRepository::class => PGVectorPDORepositoryFactory::class,
    ],
    'decorators' => [
        Redis::class => [
            \App\RedisDecorator::class,
        ],
    ],
];
