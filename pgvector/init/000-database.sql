CREATE EXTENSION vector;

CREATE TABLE posts (
   id BIGSERIAL,
   content TEXT NOT NULL,
    -- https://ollama.com/library/llama3.2
   embeddings_llama_3_2_1b vector(2048), -- 2048=count(response.body.embeddings)
   PRIMARY KEY (id)
);

-- https://github.com/pgvector/pgvector?tab=readme-ov-file#indexing
CREATE INDEX ON posts USING hnsw ((embeddings_llama_3_2_1b::halfvec(2048)) halfvec_cosine_ops);
