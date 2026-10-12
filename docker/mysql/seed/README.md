Put a database dump (`*.sql` or `*.sql.gz`) in this folder.

When the stack starts with an empty `docker/mysql/data`, the newest dump here (by file name)
is imported into `asl_laravel`. It is not imported again while `docker/mysql/data` has a database in it.

If this folder has no dump, the backend container builds the database from the migrations and
seeders instead (lookup data and one local user, `admin` / `password`).

Dumps are gitignored and excluded from the Docker build context. They contain production data: do not commit or share them.
