#!/bin/bash

psql --username postgres --no-password <<-EOSQL
    CREATE ROLE lizmap WITH LOGIN CREATEDB PASSWORD 'lizmap1234!';
    CREATE DATABASE lizmap WITH OWNER lizmap;
EOSQL

psql --username postgres --no-password -d lizmap <<-EOSQL
    CREATE EXTENSION IF NOT EXISTS postgis SCHEMA public;
EOSQL

# Let .sql.ext extension to avoid postgresql docker to run *.sql file
# Ref: look for docker-entrypoint-initdb.d in https://hub.docker.com/_/postgres/
psql --username postgres --no-password -d lizmap -f /docker-entrypoint-initdb.d/schema_openads.sql.ext
