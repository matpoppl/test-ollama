<?php

declare(strict_types=1);

use App\Bootstrap;
use App\PostsRepository;
use Lib\OllamaAPI\OllamaAPI;

require __DIR__ . '/../../vendor/autoload.php';

$bootstrap = Bootstrap::create();

$api = $bootstrap->getService(OllamaAPI::class);
$postsRepository = $bootstrap->getService(PostsRepository::class);

$xml = simplexml_load_file(__DIR__ . '/Posts.xml');

foreach ($xml as $row) {

    $chunk = strip_tags('' . $row['Body']);

    echo $chunk . "\n\n";

    $response = $api->aiEmbeddings('llama3.2:1b', $chunk);

    if (! is_array($response->embeddings[0] ?? null)) {
        continue;
    }

    $postsRepository->insert($chunk, ...$response->embeddings[0]);
}

