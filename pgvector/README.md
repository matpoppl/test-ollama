
# Links

- https://www.postgresql.org/docs/current/sql-commands.html
- http://granjow.net/postgresql.html
- https://github.com/pgvector/pgvector?tab=readme-ov-file#indexing
- https://techdocs.broadcom.com/us/en/vmware-tanzu/data-solutions/tanzu-greenplum/7/greenplum-database/ref_guide-modules-pgvector-pgvector.html

# Shell

```psql
psql -d mydb1 -Umyuser1 -W

\c
\connect new_database another_user

\h
\h [command]
```

# Queries

```sql
SHOW ALL;

SELECT datname FROM pg_database WHERE datistemplate = false;

SELECT * FROM pg_catalog.pg_tables ORDER BY tablename;

CREATE USER manuel WITH PASSWORD 'mypassword1' CREATEDB;
ALTER USER davide WITH PASSWORD 'mypassword2';

SELECT collname FROM pg_collation WHERE collprovider = 'c';
SELECT collprovider FROM pg_collation GROUP BY collprovider;
SELECT * FROM pg_collation WHERE collname like '%NZ%';
```
