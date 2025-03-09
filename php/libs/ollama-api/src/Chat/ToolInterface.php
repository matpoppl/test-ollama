<?php

declare(strict_types=1);

namespace Lib\OllamaAPI\Chat;

use JsonSerializable;
use stdClass;

interface ToolInterface
{
    public function getId(): string;
    public function getJsonSchema(): array|JsonSerializable|stdClass;
    public function call(?object $arguments): mixed;
}
