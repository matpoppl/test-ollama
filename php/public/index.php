<?php

declare(strict_types=1);

use App\Bootstrap;
use App\PostsRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Lib\OllamaAPI\OllamaAPI;

require __DIR__ . '/../vendor/autoload.php';

$bootstrap = Bootstrap::create();
$api = $bootstrap->getService(OllamaAPI::class);
$modelsList = $api->listModels();

switch (ServerRequest::fromGlobals()->getUri()->getPath()) {
    case '/list-models':
            header('Content-Type: application/json; charset=utf-8');

            die(json_encode($modelsList, JSON_THROW_ON_ERROR));
        break;
    case '/completion':
            header('Content-Type: application/json; charset=utf-8');

            $completion = $_POST['completion'] ?? [];

            die(json_encode($api->aiCompletion(
                $completion['model'] ?? '',
                $completion['prompt'] ?? '',
            ), JSON_THROW_ON_ERROR));
        break;
    case '/embeddings':
            header('Content-Type: application/json; charset=utf-8');

            $completion = $_POST['embeddings'] ?? [];

            die(json_encode($api->aiEmbeddings(
                $completion['model'] ?? '',
                $completion['prompt'] ?? '',
            )));
    case '/search':
            header('Content-Type: application/json; charset=utf-8');

            $search = $_POST['search'] ?? [];
            $embedding = $api->aiEmbeddings(
                $search['model'] ?? '',
                $search['phrase'] ?? '',
            );
            $repository = $bootstrap->getService(PostsRepository::class);

            die(json_encode([...$repository->searchByEmbedding(...$embedding->embeddings[0])]));
    case '/tool':
            header('Content-Type: application/json; charset=utf-8');

            $tool = $_POST['tool'] ?? [];
            $response = $api->aiChat(
                $tool['model'] ?? '',
                $tool['phrase'] ?? '',
            );

            die(json_encode($response));
        break;
    case '/question':
            header('Content-Type: application/json; charset=utf-8');

            $question = $_GET['question'] ?? [];
            $model = $question['model'] ?? '';
            $phrase = $question['phrase'] ?? '';

            $embedding = $api->aiEmbeddings($model, $phrase);

            $repository = $bootstrap->getService(PostsRepository::class);

            $results = [];

            foreach ($repository->searchByEmbedding(...$embedding->embeddings[0]) as $post) {

                $prompt = <<<PROMPT
Use the following pieces of context to answer the question at the end. If you don't know the answer, just say that you don't know, don't try to make up an answer.

In addition to giving an answer, also return a score of how fully it answered the user's question. This should be in the following format:

Question: [question here]
Helpful Answer: [answer here]
Score: [score between 0 and 100]

How to determine the score:
- Higher is a better answer
- Better responds fully to the asked question, with sufficient level of detail
- If you do not know the answer based on the context, that should be a score of 0
- Don't be overconfident!

Example #1

Context:
---------
Apples are red
---------
Question: what color are apples?
Helpful Answer: red
Score: 100

Example #2

Context:
---------
it was night and the witness forgot his glasses. he was not sure if it was a sports car or an suv
---------
Question: what type was the car?
Helpful Answer: a sports car or an suv
Score: 60

Example #3

Context:
---------
Pears are either red or orange
---------
Question: what color are apples?
Helpful Answer: This document does not answer the question
Score: 0

Begin!

Context:
---------
{$post->content}
---------
Question: {$phrase}
Helpful Answer:
PROMPT;

                $result = $api->aiCompletion($model, $prompt);
                $result->context = '---REMOVED---';

                $results[] = $result;
            }

            die(json_encode($results, JSON_THROW_ON_ERROR));

        break;
}

?>

<form method="GET" action="/list-models" target="_blank">
    <fieldset>
        <button type="submit">List models</button>
    </fieldset>
</form>

<form method="POST" action="/completion" target="_blank">
    <fieldset>
        <legend>Generate completion</legend>
        <div>
            <label for="completion[model]">LLM Model</label>
            <select id="completion[model]" name="completion[model]" required>
                <option value="">-- choose --</option>
                <?php foreach ($modelsList as $model): ?>
                <option value="<?= $model->name ?>"><?= "{$model->name} ({$model->getReadableSize()})" ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="completion[prompt]">Prompt: </label>
            <input type="tel" id="completion[prompt]" name="completion[prompt]" required size="90" />
        </div>

        <button type="submit">Generate completion</button>

    </fieldset>
</form>

<form method="POST" action="/embeddings" target="_blank">
    <fieldset>
        <legend>Generate embeddings</legend>
        <div>
            <label for="embeddings[model]">LLM Model</label>
            <select id="embeddings[model]" name="embeddings[model]" required>
                <option value="">-- choose --</option>
                <?php foreach ($modelsList as $model): ?>
                <option value="<?= $model->name ?>"><?= "{$model->name} ({$model->getReadableSize()})" ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="embeddings[prompt]">Prompt: </label>
            <input id="embeddings[prompt]" name="embeddings[prompt]" required size="90" />
        </div>

        <button type="submit">Generate embeddings</button>

    </fieldset>
</form>

<form method="POST" action="/search" target="_blank">
    <fieldset>
        <legend>Search</legend>

        <div>
            <label for="search[model]">LLM Model</label>
            <select id="search[model]" name="search[model]" required>
                <option value="">-- choose --</option>
                <?php foreach ($modelsList as $model): ?>
                    <option value="<?= $model->name ?>"><?= "{$model->name} ({$model->getReadableSize()})" ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="search[phrase]">Search phrase: </label>
            <input type="search" id="search[phrase]" name="search[phrase]" required size="90" />
        </div>

        <button type="submit">Search</button>
    </fieldset>
</form>

<form method="GET" action="/question" target="_blank">
    <fieldset>
        <legend>Question</legend>

        <div>
            <label for="question[model]">LLM Model</label>
            <select id="question[model]" name="question[model]" required>
                <option value="">-- choose --</option>
                <?php foreach ($modelsList as $model): ?>
                    <option value="<?= $model->name ?>"><?= "{$model->name} ({$model->getReadableSize()})" ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="question[phrase]">Search phrase: </label>
            <input type="search" id="question[phrase]" name="question[phrase]" required size="90" />
        </div>

        <button type="submit">Answer</button>
    </fieldset>
</form>

<form method="POST" action="/tool" target="_blank">
    <fieldset>
        <legend>Tools</legend>

        <div>
            <label for="tool[model]">LLM Model</label>
            <select id="tool[model]" name="tool[model]" required>
                <option value="">-- choose --</option>
                <?php foreach ($modelsList as $model): ?>
                    <option value="<?= $model->name ?>"><?= "{$model->name} ({$model->getReadableSize()})" ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="tool[phrase]">Search phrase: </label>
            <input type="search" id="tool[phrase]" name="tool[phrase]" required size="90"
                placeholder="What is the current temperature in Warsaw, Poland?"/>
        </div>

        <button type="submit">Use tool</button>
    </fieldset>
</form>
