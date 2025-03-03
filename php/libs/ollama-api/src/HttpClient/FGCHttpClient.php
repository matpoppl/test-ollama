<?php

declare(strict_types=1);

namespace Lib\OllamaAPI\HttpClient;

use function file_get_contents;
use function stream_context_create;

class FGCHttpClient implements HttpClient
{
    public function __construct(private readonly string $baseUrl)
    {}

    public function get(string $path): string
    {
        return $this->request('GET', $path);
    }

    private function request(string $method, string $path): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'timeout' => 1.0,
                'ignore_errors' => true,
            ]
        ]);

        $http_response_header = null;
        $body = file_get_contents("{$this->baseUrl}{$path}", false, $context);

        var_dump($http_response_header);

        return $body;
    }
}
