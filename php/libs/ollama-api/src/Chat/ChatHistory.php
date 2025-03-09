<?php

declare(strict_types=1);

namespace Lib\OllamaAPI\Chat;

use JsonSerializable;

use stdClass;

class ChatHistory implements JsonSerializable
{
    /** @var array<array<string, string>> */
    private array $messages = [];

    public function add(string $role, string $content): void
    {
        $this->messages[] = (object) [
            'role' => $role,
            'content' => $content,
        ];
    }

    public function addMessage(stdClass $message): void
    {
        $this->messages[] = $message;
    }

    public function jsonSerialize(): array
    {
        return $this->messages;
    }
}
