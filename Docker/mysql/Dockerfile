FROM mysql:5.7
LABEL maintainer="gopinath <gopinath2nr@gmail.com>"

# Copy the database initialization script to the special folder
# MySQL will execute files in this folder on first startup
COPY dbinit/tables.sql /docker-entrypoint-initdb.d/