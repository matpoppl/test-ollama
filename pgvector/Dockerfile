FROM pgvector/pgvector:pg17

RUN mkdir -p /var/lib/postgresql/data \
    && chown -R postgres:postgres /var/lib/postgresql/data

ENV PGDATA /var/lib/postgresql/data

ENV POSTGRES_INITDB_ARG="--pgdata=/var/lib/postgresql/data --encoding=UTF8"

COPY ./init/*.sql /docker-entrypoint-initdb.d/
