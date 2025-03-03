<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOStatement;

use function implode;

class PostsRepository
{
    private ?PDOStatement $stmtInsert = null;

    public function __construct(private readonly PGVectorPDO $pdo)
    {}

    public function insert(string $content, float ...$embeddings): string
    {
        $this->stmtInsert ??= $this->pdo->prepare('INSERT INTO posts (content, embeddings_llama_3_2_1b) VALUES (?, ?)');
        $this->stmtInsert->execute([
            $content,
            '['.implode(',', $embeddings).']',
        ]);

        return (string) $this->pdo->lastInsertId();
    }

    /**
     * @return iterable<object{
     *     id: string,
     *     content: string,
     * }>
     */
    public function searchByEmbedding(float ...$embeddings): iterable
    {
        $stmt = $this->pdo->prepare('SELECT id, content FROM posts ORDER BY embeddings_llama_3_2_1b <=> ? LIMIT 5');
        $stmt->execute([
            '['.implode(',', $embeddings).']'
        ]);
        $stmt->setFetchMode(PDO::FETCH_OBJ);

        return $stmt;
    }

    /**
     * @return iterable<object{
     *     id: string,
     *     content: string,
     * }>
     */
    public function fetchAll(): iterable
    {
        return $this->pdo->query('SELECT id, content FROM posts', PDO::FETCH_OBJ);
    }
}
