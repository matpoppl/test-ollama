<?php

declare(strict_types=1);

namespace Lib\OllamaAPI\Chat;

use JsonSerializable;
use UnexpectedValueException;

use function array_map;
use function array_values;

class Tools implements JsonSerializable
{
    /** @var array<string, ToolInterface> */
    private array $tools;

    public function register(ToolInterface ...$tools): void
    {
        foreach ($tools as $tool) {
            $this->tools[$tool->getId()] = $tool;
        }
    }

    public function get(string $id): ToolInterface
    {
        return $this->tools[$id] ?? throw new UnexpectedValueException("Tool $id not found");
    }

    public function jsonSerialize(): array
    {
        return array_map(
            fn(ToolInterface $tool) => $tool->getJsonSchema(),
            array_values($this->tools),
        );
    }
}
