CREATE EXTENSION vector;

CREATE TABLE posts (
   sid CHAR(64) NOT NULL,
   content TEXT NOT NULL,
    -- https://ollama.com/library/llama3.2
   embs_paraphrase vector(768) NULL, -- 768=count(response.body.embeddings)
   PRIMARY KEY (sid)
);

-- https://github.com/pgvector/pgvector?tab=readme-ov-file#indexing
CREATE INDEX ON posts USING hnsw ((embs_paraphrase::halfvec(2048)) halfvec_cosine_ops);
CREATE INDEX ON posts USING hnsw ((embs_paraphrase::halfvec(2048)) halfvec_l2_ops);
