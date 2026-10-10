#!/bin/sh
# First-run setup for the dev container. Every step is skipped once it has been done.
set -e

cd /var/www/html

# storage/ is gitignored, so a fresh clone has none of the folders Laravel needs.
mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ ! -f .env ]; then
    echo "[asl] .env not found, copying .env.example"
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    echo "[asl] vendor/ not found, running composer install (first run takes a few minutes)"
    # composer.lock pins tymon/jwt-auth 1.0.2 and lcobucci/jwt 3.3.3, which declare PHP 7 only.
    # Production runs that same lock on PHP 8.1, so the PHP version check is skipped here too.
    composer install --no-interaction --prefer-dist --ignore-platform-req=php
fi

if ! grep -q '^APP_KEY=.\+' .env; then
    echo "[asl] generating APP_KEY"
    php artisan key:generate --force --ansi
fi

if ! grep -q '^JWT_SECRET=.\+' .env; then
    echo "[asl] generating JWT_SECRET"
    php artisan jwt:secret --force --ansi
fi

# A database that came from a dump already has its tables and is left alone.
# A database with no tables at all (no dump in docker/mysql/seed) is built from the migrations and seeders.
tables="$(php -r '
    try {
        $pdo = new PDO("mysql:host=" . getenv("DB_HOST") . ";port=" . getenv("DB_PORT"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));
        $q = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ?");
        $q->execute([getenv("DB_DATABASE")]);
        echo $q->fetchColumn();
    } catch (Throwable $e) {
        echo "unknown";
    }
' 2>/dev/null)"

if [ "$tables" = "0" ]; then
    echo "[asl] database ${DB_DATABASE} is empty, running migrations and seeders (login: admin / password)"
    php artisan migrate --force --seed --ansi
fi

exec "$@"
