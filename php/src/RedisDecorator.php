<?php

declare(strict_types=1);

namespace App;

use Redis;

class RedisDecorator
{
    public function __invoke(Redis $service): void
    {
        $service->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_JSON);
    }
}
