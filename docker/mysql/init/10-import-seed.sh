#!/bin/bash
# Sourced by the MySQL image entrypoint, which only runs init scripts when the
# data directory (docker/mysql/data) is empty. An existing database is never touched.
# Imports the newest *.sql or *.sql.gz from docker/mysql/seed (mounted at /seed).

seed="$(ls -1 /seed/*.sql /seed/*.sql.gz 2>/dev/null | sort | tail -n 1)"

if [ -z "$seed" ]; then
    echo "[asl-seed] no dump in docker/mysql/seed, leaving ${MYSQL_DATABASE} empty; the backend container will build it from migrations and seeders"
    return 0 2>/dev/null || exit 0
fi

echo "[asl-seed] importing $(basename "$seed") into ${MYSQL_DATABASE}, this can take a few minutes..."
if [[ "$seed" == *.gz ]]; then
    gunzip -c "$seed" | mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}"
else
    mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" < "$seed"
fi
echo "[asl-seed] import finished"
