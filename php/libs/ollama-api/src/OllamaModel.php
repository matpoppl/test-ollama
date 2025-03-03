<?php

declare(strict_types=1);

namespace Lib\OllamaAPI;

use DateTimeInterface;
use JsonSerializable;

class OllamaModel implements JSONSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly string $model,
        public readonly DateTimeInterface $modified_at,
        public readonly int $size,
        public readonly ?object $details,
    ) {}

    public function jsonSerialize(): mixed
    {
        return (object) [
            'name' => $this->name,
            'model' => $this->model,
            'modified_at' => $this->modified_at->format(DATE_ATOM),
            'size' => $this->size,
            'details' => $this->details,
        ];
    }

    public function getReadableSize(): string
    {
        return sprintf('%.1fGiB', $this->size / 1024 / 1024 / 1024);
    }
}
