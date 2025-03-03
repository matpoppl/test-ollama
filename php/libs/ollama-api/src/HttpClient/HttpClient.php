<?php

namespace Lib\OllamaAPI\HttpClient;

interface HttpClient
{
    public function get(string $path): string;
}
