# ASL Shipping Line API

Laravel 8 JSON API for the ASL vehicle shipping system: vehicles, containers (exports), customers, consignees,
invoices, claims, reports. Used by the web app ([asl-frontend](https://github.com/mrstarengineer/asl-frontend)) and the mobile app.

**Stack:** PHP 8.1, Laravel 8, MySQL 5.7, Redis, JWT auth, S3 file storage.

## Run locally (Docker)

```bash
docker compose up -d
```

| URL | |
|---|---|
| http://localhost:8080 | API, endpoints under `/api/v1` |
| http://localhost:8181 | Adminer (server `db`, user `asl`, password `secret`, database `asl_laravel`) |

The first start creates `.env`, installs composer packages and generates the app and JWT keys.

**Database.** Data is stored in `docker/mysql/data`. If that folder is empty when the stack starts, the newest
`.sql` / `.sql.gz` file in `docker/mysql/seed/` is imported. With no dump there, the database is built from the migrations
and seeders instead and you can log in with `admin` / `password`. To start again, stop the stack and delete `docker/mysql/data`.
Dumps are gitignored and must stay out of the repo.

To run the API and the frontend together, keep both repos side by side and use the `manage-docker.sh` script in the parent folder.

## Common commands

```bash
docker exec asl-backend php artisan migrate
docker exec asl-backend php artisan route:list
docker exec asl-backend composer install --ignore-platform-req=php
docker compose logs -f backend
```

## Project structure

```
routes/api.v1.php              All API routes (/api/v1)
app/Http/Controllers/Api/V1    Controllers, grouped by domain
app/Services                   Business logic and queries
app/Models                     Eloquent models
app/Presenters, app/Transformer  Response shaping
app/Exports                    Excel exports
app/Enums                      Roles, statuses and other constants
app/Console/Commands           Scheduled and one-off commands
resources/views/pdf            PDF templates
database/migrations            Schema changes
docker/                        Local Docker config (nginx, php, mysql)
```

## Notes

- Do not run `php artisan config:cache`, `route:cache` or `optimize`; the code reads `env()` directly.
- Locally files use the `public` disk. Set `FILESYSTEM_DRIVER=s3` and the `AWS_*` keys in `.env` to use the real bucket.
- `AGENTS.md` has the conventions in more detail (written for AI coding agents, useful for people too).

## Deploy

```bash
./deploy-live-aws.sh           # deploy main to production
./deploy-live-aws.sh status    # what is live, what is pending
./deploy-live-aws.sh rollback
```
