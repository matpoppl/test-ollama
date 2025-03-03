<?php

declare(strict_types=1);

use App\ConfigBuilder;
use App\PGVectorPDO;
use Lib\ServiceContainer\ServiceContainerFactory;

require __DIR__ . '/../vendor/autoload.php';

//header('Content-Type: application/json; charset=utf-8');

$sm = (new ServiceContainerFactory())->create(
    (new ConfigBuilder)
        ->scanPath(__DIR__ . '/../configs/*.php')
        ->build()
);

echo json_encode($sm->get(PGVectorPDO::class)->query('SHOW ALL', PDO::FETCH_ASSOC)->fetchAll());
