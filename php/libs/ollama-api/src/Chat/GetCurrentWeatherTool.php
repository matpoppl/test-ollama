<?php

declare(strict_types=1);

namespace Lib\OllamaAPI\Chat;

use stdClass;

use function str_contains;

class GetCurrentWeatherTool implements ToolInterface
{
    public function getId(): string
    {
        return 'get_current_weather';
    }

    public function getJsonSchema(): stdClass
    {
        return (object) [
            'type' => 'function',
            'function' => [
                'name' => $this->getId(),
                'description' => 'Get the current weather for a location',
                'required' => ['location', 'format'],
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'location' => [
                            'type' => 'string',
                            'description' => 'The location to get the weather for, e.g. San Francisco, CA',
                        ],
                        'format' => [
                            'type' => 'string',
                            'description' => 'The format to return the weather in, e.g. "celsius" or "fahrenheit"',
                            'enum' => ['celsius', 'fahrenheit'],
                        ]
                    ]
                ]
            ]
        ];
    }

    public function call(?object $arguments): string
    {
        if (! $arguments) {
            return "Error: {$this->getId()} requires arguments";
        }

        $location = $arguments->location ?? 'Unknown';
        $format = $arguments->format ?? 'Unknown';

        if (str_contains($location, 'Warsaw')) {
            $temp = match ($format) {
                'celsius' => '36°C',
                default => '96.8°F',
            };
        } else {
            $temp = match ($format) {
                'celsius' => '5°C',
                default => '41°F',
            };
        }

        return $temp;
    }
}
