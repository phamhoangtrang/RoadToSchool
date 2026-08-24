# RoadToSchool

RoadToSchool is a bilingual learning-management application for students, instructors and administrators. The original Blade interface and workflows are preserved while the runtime has been upgraded to a supported modern stack.

## Technology

- PHP 8.5, Laravel 13 and PHPUnit 13
- MySQL 8.4 and Eloquent ORM
- Blade, Bootstrap 5, jQuery 3.7 and Vite 8
- Laravel Reverb for comments, notifications and conversations
- English and Vietnamese interfaces

## Local setup with Docker

Requirements: Docker Engine with the Compose plugin and Google Chrome if you want to run the browser smoke test.

```bash
cp .env.example .env
docker compose -f compose.local.yaml build
docker compose -f compose.local.yaml run --rm --no-deps app composer install
docker compose -f compose.local.yaml run --rm --no-deps app php artisan key:generate
docker compose -f compose.local.yaml up -d database
docker compose -f compose.local.yaml run --rm app php artisan migrate --seed
docker compose -f compose.local.yaml up -d
```

Open <http://localhost:8000>. Reverb listens on <http://localhost:8080> and MySQL is exposed locally on port `3307`.

The local demo seeder creates these accounts; all use password `123456`:

| Role | Email |
| --- | --- |
| Administrator | `admin@roadtoschool.local` |
| Instructor | `instructor@roadtoschool.local` |
| Student | `student@roadtoschool.local` |

`php artisan migrate --seed` is intended for a new database. To deliberately rebuild the local demo database, use `php artisan migrate:fresh --seed`; this deletes existing local data.

## Development commands

```bash
# Service status and logs
docker compose -f compose.local.yaml ps
docker compose -f compose.local.yaml logs -f app reverb

# PHP tests and dependency audit
docker compose -f compose.local.yaml exec -T app php artisan test
docker compose -f compose.local.yaml exec -T app composer audit

# Frontend build and audit in the pinned Node 24 container
docker compose -f compose.local.yaml run --rm --no-deps assets
docker compose -f compose.local.yaml run --rm --no-deps assets npm audit

# Browser smoke test (requires Node.js and Google Chrome on the host)
npm run test:browser
```

Generated frontend files are written to `public/build` and are intentionally ignored by Git. Configure production secrets in the deployment environment; never commit a populated `.env` file.

## Optional services

The recommendation service is disabled when `RECOMMENDATION_ENDPOINT` is empty. Configure the `RECOMMENDATION_*` variables to enable it. Reverb is the default broadcaster; Pusher-compatible credentials are no longer embedded in browser assets.
