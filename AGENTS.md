# asl-laravel (ASL API)

Laravel 8.73 JSON API on PHP 8.1 and MySQL 5.7. JWT auth (`tymon/jwt-auth`), roles/permissions (`spatie/laravel-permission`),
files on S3, Excel exports (`maatwebsite/excel`), PDFs (`barryvdh/laravel-dompdf`). Consumed by the `frontend` repo and a mobile app.

## Commands (run in the container)

```bash
docker exec asl-backend php artisan <cmd>         # route:list, tinker, migrate, make:migration ...
docker exec asl-backend composer <cmd>            # add --ignore-platform-req=php to install/require
docker exec asl-db mysql -uasl -psecret asl_laravel -e "SQL"
docker compose logs -f backend                    # or: tail storage/logs/laravel.log
```

There are no real tests (`tests/` has only the Laravel examples). Verify changes with `curl` against http://localhost:8080.

## Request path

`routes/api.v1.php` → `app/Http/Controllers/Api/V1/<Domain>/<X>Controller` → `app/Services/<Domain>/<X>Service` → `app/Models/<X>` → `app/Presenters/<X>Presenter` → JSON

- All routes are in `routes/api.v1.php`, prefixed `/api/v1`. String routes like `'Vehicle\VehicleController@x'` resolve under `App\Http\Controllers\Api\V1`.
- The file has two blocks: "Anonymous routes" (no auth: downloads, exports, uploads) and the `jwt.verify` group (everything else). Specific routes must be declared before the matching `apiResource`.
- Controllers stay thin: validate with `$this->validate()`, wrap writes in `DB::beginTransaction()`, call the service, return.
- Services hold the queries and business rules. List methods take `array $filters` and return a paginator (`limit`, default 20; `-1` means up to 1000).
- Presenters (`nahid/presento`) shape output: `present()` returns `['field', 'alias' => 'relation.field']`. Lists go through `PaginatorPresenter(...)->presentBy(XPresenter::class)`.
- Responses: lists return the presented paginator directly; writes return `api($data)->success('msg', 201)` / `api()->fails($msg, 400)` (`app/Supports/ApiJsonResponse.php`).
- Helpers in `app/Supports/helpers.php`: `api()`, `debug_log()`, `store_activity()` (audit log), `auth()` (overridden to the JWT guard).
- Constants live in `app/Enums` (`Roles`, `VehicleStatus`, ...). `app/Exports` = Excel, `resources/views/pdf` = PDF templates, `app/Transformer` = older output shaping still used by some endpoints.

## Things that will bite you

- `composer.lock` pins `tymon/jwt-auth` 1.0.2, which declares PHP 7 only. Production runs it on PHP 8.1 anyway. Use `--ignore-platform-req=php`; do not run a blanket `composer update`.
- Code calls `env()` directly (`AWS_S3_BASE_URL`, `APP_URL`, `CHINA_SHOW_ROLES`, `CHINA_CUSTOMER_USER_IDS`). Never run `config:cache`, `route:cache` or `optimize`: `env()` then returns null, and `routes/web.php` has closures.
- Role and location rules are hard-coded in services: `users.role` is an int (`App\Enums\Roles`: 0 master admin … 3 customer …), and location id 16 (China) is filtered per role in Vehicle, Export, Customer, Consignee, Yard, Location and Report services. Copy the existing block when adding a list query on those entities.
- Permissions are not enforced by middleware. `AuthController::getPermissionList()` sends a `{identifier: bool}` map at login and the frontend hides UI from it. Role 0 gets everything.
- MySQL runs non-strict (`'strict' => false`); many queries rely on it.
- The default disk is `s3`, and transformers call `Storage::exists()` per row (`VehicleTransformer`, `ExportTransformer`). With no AWS keys that is a 500 on vehicle and invoice endpoints, so Docker sets `FILESYSTEM_DRIVER=public`: lists work, photos are missing, uploads land in `storage/app/public`. For real files put `FILESYSTEM_DRIVER=s3` and the `AWS_*` keys in `.env`, then `docker compose up -d`.
- `storage/` is gitignored entirely; the Docker entrypoint creates it. `.env`, `APP_KEY` and `JWT_SECRET` are also created on first start.
- Two ways to get a database. With a dump in `docker/mysql/seed/` it is imported (full production data). With no dump the entrypoint runs `migrate --seed` on the empty database: `ReferenceDataSeeder` (lookup rows from `database/seeders/data/*.json`, production ids kept) and `LocalAdminSeeder` (`admin` / `password`, local env only).
- Migrations build the production schema from scratch; `2026_10_09_000000_align_schema_with_production` adds what production had gained by hand and skips whatever already exists. Write new migrations the same way if the change may already be on production. Known leftovers: `users.role_id` and `vehicles.shipper_id` exist only in migrations (unused), `export_images` column types differ slightly, the two `vw_*` views exist only in migrations (unused).
- New lookup rows the app needs (a permission, a location) go in the matching `database/seeders/data/*.json` as well as in a migration for production.
- `UserSeeder`, `LocationTableSeeder`, `RolesTableSeeder`, `PermissionsTableSeeder` and the other old seeders are not called and do not match production. Do not run or copy them.
- `app/Console/Commands/*Migration*`, `Sync*` and the `amaya_db` connection are one-off imports from the legacy system. Only `sync:container_status` is scheduled.
- `resources/js`, `webpack.mix.js`, `package.json`, `SpaController` are leftovers of an old bundled SPA. Not used.
- `Dockerfile` (PHP 8.2) is not used by anything. Local dev uses `dev.Dockerfile`; production is not containerised.

## Docker files

`docker-compose.yml`, `dev.Dockerfile`, `docker/php/entrypoint.sh` (first-run setup), `docker/nginx/default.conf`,
`docker/mysql/{my.cnf,init/10-import-seed.sh,seed/,data/}`. See `../.claude/skills/asl-docker/SKILL.md`.

## Deploy

`./deploy-live-aws.sh` (also `status`, `rollback`): the server pulls `main`, optionally composer install and migrate, reloads php8.1-fpm. Live at https://connect.amayausedcars.com. Run only when asked.
