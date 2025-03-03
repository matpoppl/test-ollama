<?php

declare(strict_types=1);

namespace App;

class ConfigBuilder
{
    /** @var string[] */
    private array $globPatterns = [];

    public function scanPath(string $globPattern): self
    {
        $this->globPatterns[] = $globPattern;

        return $this;
    }

    public function build(): array
    {
        $config = [];

        foreach ($this->globPatterns as $pattern) {
            foreach (glob($pattern) as $pathname) {
                $basename = basename($pathname, '.php');
                $config[$basename] = array_merge_recursive($config[$basename] ?? [], require $pathname);
            }
        }

        return $config;
    }
}
