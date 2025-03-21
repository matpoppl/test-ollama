<?php

declare(strict_types=1);

namespace Lib\OllamaAPI;

use DateTimeImmutable;
use Lib\OllamaAPI\Chat\ChatHistory;
use Lib\OllamaAPI\Chat\GetCurrentWeatherTool;
use Lib\OllamaAPI\Chat\Tools;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

class OllamaAPI
{
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $httpRequestFactory,
    ) {
    }

    /**
     * @return OllamaModel[]
     * @throws ClientExceptionInterface
     */
    public function listModels(): array
    {
        $request = $this->httpRequestFactory->createRequest('GET', 'tags');
        $response = $this->httpClient->sendRequest($request);
        $json = json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);

        return array_map(
            fn(object $model) => new OllamaModel(
                name: $model->name,
                model: $model->name,
                modified_at: new DateTimeImmutable($model->modified_at),
                size: $model->size,
                details: $model->details,
            ),
            $json->models,
        );
    }

    /**
     * @return object{
     *     model: string,
     *     created_at: string,
     *     response: string,
     *     done: string,
     *     done_reason: string,
     *     context: array,
     * }
     * @throws ClientExceptionInterface
     */
    public function aiCompletion(string $model, string $prompt): object
    {
        $stream = $this->httpRequestFactory->createStream();
        $stream->write(json_encode([
            'model' => $model,
            'prompt' => $prompt,
            'stream' => false,
        ]));
        $request = $this->httpRequestFactory->createRequest('POST', 'generate');
        $response = $this->httpClient->sendRequest($request->withBody($stream));

        return json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return object{
     *     model: string,
     *     embeddings: array<float[]>,
     *     total_duration: int,
     *     load_duration: int,
     *     prompt_eval_count: int,
     * }
     * @throws ClientExceptionInterface
     */
    public function aiEmbeddings(string $model, string $input): object
    {
        $stream = $this->httpRequestFactory->createStream();
        $stream->write(json_encode([
            'model' => $model,
            'input' => $input,
            'stream' => false,
        ]));
        $request = $this->httpRequestFactory->createRequest('POST', 'embed');
        $response = $this->httpClient->sendRequest($request->withBody($stream));

        return json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
    }

    public function aiChat(string $model, string $input): object
    {
        $sendRequest = function (string $model, ChatHistory $history, ?Tools $tools): object
        {
            $stream = $this->httpRequestFactory->createStream();
            $stream->write(json_encode([
                'model' => $model,
                'stream' => false,
                'messages' => $history->jsonSerialize(),
                'tools' => $tools?->jsonSerialize() ?? [],
            ]));
            $request = $this->httpRequestFactory->createRequest('POST', 'chat');
            $response = $this->httpClient->sendRequest($request->withBody($stream));

            return json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        };

        $history = new ChatHistory();
        $tools = new Tools();
        $tools->register(new GetCurrentWeatherTool());
        $history->add('user', $input);
        $response = $sendRequest($model, $history, $tools);
        $history->addMessage($response->message);

        foreach ($response->message->tool_calls ?? [] as $tool_call) {
            if ($function = $tool_call->function ?? null) {
                $history->add(
                    'tool',
                    $tools->get($function->name ?? 'Name missing')->call($function->arguments ?? null)
                );
            }
        }

        $response = $sendRequest($model, $history, null);
        $history->addMessage($response->message);
        $response->__HISTORY__ = $history->jsonSerialize();

        return $response;
    }
}
