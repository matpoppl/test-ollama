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

    public function insert(string $sid, string $content, float ...$embeddings): string
    {
        $sql = <<<SQL
INSERT INTO posts (sid, content, embs_paraphrase)
VALUES (?, ?, ?)
SQL;

        $this->stmtInsert ??= $this->pdo->prepare($sql);
        $this->stmtInsert->execute([
            $sid,
            $content,
            '['.implode(',', $embeddings).']',
        ]);

        return $sid;
    }

    /**
     * @return iterable<object{
     *     id: string,
     *     content: string,
     * }>
     */
    public function searchByEmbedding(string $index, float ...$embeddings): iterable
    {
        $orderBy = match ($index) {
            'l2' => 'embs_paraphrase <-> :q',
            'l2_halfvec' => 'embs_paraphrase::halfvec(2048) <-> :q::halfvec(2048)',
            'cosine' => 'embs_paraphrase <=> :q',
            // cosine_halfvec
            default => 'embs_paraphrase::halfvec(2048) <=> :q::halfvec(2048)'
        };

        $stmt = $this->pdo->prepare("SELECT id, content FROM posts ORDER BY {$orderBy} LIMIT 20");
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
