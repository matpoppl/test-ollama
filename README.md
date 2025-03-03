# Install

```shell
docker compose up

docker exec myai-ollama-1 ollama run llama3.2:1b

docker exec -it myai-php-1 composer install

# Only Posts.xml
unzip 3dprinting.stackexchange.com.zip -d public/tasks

docker exec -it myai-php-1 php public/tasks/embed_posts.php
```

# Datasets

- https://archive.org/details/stack-exchange-2022-03-07

# Links

- https://github.com/langchain-ai/langchain/blob/master/libs/langchain/langchain/chains/question_answering/map_rerank_prompt.py
- https://hub.docker.com/_/postgres
