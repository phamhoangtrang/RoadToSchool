# RoadToSchool

**English** | [Tiếng Việt](README.vi.md)

RoadToSchool is a bilingual learning-management system for students, instructors, and administrators. It supports course discovery, enrollment and checkout, video lectures, quizzes, learning progress, moderation, role-based permissions, and real-time interactions. The original Blade user experience is preserved on a modern Laravel runtime.

## Main features

| Area | Capabilities |
| --- | --- |
| Student | Register and sign in, browse and filter courses, view instructor profiles, use the cart and checkout, follow lectures, track progress, take quizzes, rate courses, comment, receive notifications, and contact support |
| Instructor | View an instructor dashboard, create courses, add video lectures and quizzes, manage course content, and review enrolled students |
| Administrator | Manage users and instructors, permissions, categories, courses, lecture approval, bills, instructor rankings, and waiting conversations |
| Shared | English/Vietnamese interface, password reset and email verification flows, responsive Blade views, YouTube metadata, recommendations, and Reverb-powered real-time events |

## Architecture

```text
Browser
  ├─ Blade + Bootstrap + jQuery assets built by Vite/esbuild
  ├─ HTTP/AJAX ──> Laravel routes ──> Controllers/Requests
  │                                      ├─ Eloquent ──> MySQL
  │                                      └─ Services ──> YouTube / recommendation API
  └─ Laravel Echo <── WebSocket ──> Laravel Reverb
```

The application uses Laravel MVC rather than a single-page application. Authorization is enforced through authentication, role/permission middleware, ownership checks, and server-side validation. Checkout, bill activation, lecture approval, quiz submission, and other multi-record operations use transactions where consistency matters.

## Technology stack

The following versions are the currently verified local baseline:

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.5.9, Laravel 13.26.1, Eloquent ORM, Blade |
| Database | MySQL 8.4; SQLite in-memory for automated tests |
| Realtime | Laravel Reverb 1.11, Laravel Echo 2.4, Pusher protocol client |
| Frontend | Bootstrap 5.3, jQuery 3.7, Axios 1.x, Sass, Vite 8.2, esbuild |
| UI libraries | Chart.js, DataTables, Summernote, Selectize, jQuery UI, Moment.js |
| Testing | PHPUnit 13.3 and Playwright Core 1.62 browser smoke tests |
| Tooling | Composer 2.10, Node.js 24, Docker Compose |

## Quick start with Docker

Docker is the recommended and reproducible development environment.

### Requirements

- Docker Engine with the Docker Compose plugin
- Git
- Google Chrome on the host only when running browser smoke tests

### First installation

```bash
git clone https://github.com/phamhoangtrang/RoadToSchool.git
cd RoadToSchool
git checkout minhle

cp .env.example .env
docker compose -f compose.local.yaml build app reverb
docker compose -f compose.local.yaml run --rm --no-deps app composer install
docker compose -f compose.local.yaml run --rm --no-deps app php artisan key:generate
docker compose -f compose.local.yaml up -d --wait database
docker compose -f compose.local.yaml run --rm --no-deps app php artisan migrate --seed
docker compose -f compose.local.yaml up -d
```

Open <http://localhost:8000> after the containers start.

### Local services

| Service | Address | Purpose |
| --- | --- | --- |
| Laravel application | <http://localhost:8000> | Main web application |
| Laravel Reverb | `ws://localhost:8080` | Comments, notifications, discussions, and conversations |
| MySQL | `127.0.0.1:3307` | Local database exposed from the container |
| Assets | One-shot container | Runs `npm ci` and creates the production frontend build |

The application connects to MySQL internally through `database:3306`. Generated frontend files are written to `public/build` and are intentionally ignored by Git.

### Demo accounts

The local demo seeder creates representative data and these accounts. All accounts use password `123456`.

| Role | Email |
| --- | --- |
| Administrator | `admin@roadtoschool.local` |
| Instructor | `instructor@roadtoschool.local` |
| Student | `student@roadtoschool.local` |

The demo includes categories, three accepted courses, lectures, a quiz, student progress, cart items, a comment, and the default permission assignments.

### Starting and stopping

```bash
# Start or rebuild the complete environment
docker compose -f compose.local.yaml up -d --build

# Inspect status
docker compose -f compose.local.yaml ps

# Follow application and realtime logs
docker compose -f compose.local.yaml logs -f app reverb

# Stop containers while preserving the MySQL volume
docker compose -f compose.local.yaml down
```

Do not add `-v` to `docker compose down` unless the local MySQL data should also be deleted.

## Updating an existing checkout

```bash
git checkout minhle
git pull --ff-only origin minhle
docker compose -f compose.local.yaml build app reverb
docker compose -f compose.local.yaml run --rm --no-deps app composer install
docker compose -f compose.local.yaml run --rm --no-deps assets
docker compose -f compose.local.yaml up -d --wait database
docker compose -f compose.local.yaml run --rm --no-deps app php artisan migrate
docker compose -f compose.local.yaml up -d
```

## Development commands

```bash
# Run Artisan or Composer inside the application container
docker compose -f compose.local.yaml exec -T app php artisan about
docker compose -f compose.local.yaml exec -T app php artisan route:list
docker compose -f compose.local.yaml exec -T app composer audit

# Reinstall pinned frontend dependencies and build production assets
docker compose -f compose.local.yaml run --rm --no-deps assets

# Audit all npm dependencies
docker compose -f compose.local.yaml run --rm --no-deps assets npm audit

# Clear Laravel caches during development
docker compose -f compose.local.yaml exec -T app php artisan optimize:clear
```

For frontend hot reload on the host, install Node.js 24 and run:

```bash
npm ci
npm run dev
```

The production asset pipeline builds the main Vite entry points plus two compatibility bundles:

- `resources/sass/app.scss` and `resources/sass/admin.scss`
- `resources/js/app.js`
- `resources/js/legacy.js`
- `resources/js/admin.js`

## Tests and quality checks

### PHP test suite

```bash
docker compose -f compose.local.yaml exec -T app php artisan test
```

The current baseline contains 49 passing tests and 251 assertions. It covers account security, route permissions, administration, checkout and bills, courses and lectures, interactions, notifications, ratings, quizzes, seeding, recommendation handling, and YouTube metadata.

Tests use an in-memory SQLite database and do not modify the Docker MySQL database.

### Browser smoke test

Install Node.js dependencies on the host, make sure Google Chrome is available, start the application, and run:

```bash
npm ci
npm run test:browser
```

The smoke test signs in as administrator, instructor, and student, visits the main pages for each role, reports browser/HTTP errors, and writes screenshots to `/tmp`. Optional overrides are available:

```bash
APP_URL=http://127.0.0.1:8000 \
CHROME_PATH=/usr/bin/google-chrome \
npm run test:browser
```

## Environment configuration

Copy `.env.example` to `.env`. Never commit the populated `.env` file.

| Variables | Description |
| --- | --- |
| `APP_*` | Application URL, environment, locale, timezone, debug mode, and encryption key |
| `DB_*` | MySQL host, port, database, and credentials |
| `BROADCAST_CONNECTION` | Set to `reverb` in the provided local environment |
| `REVERB_*` | Server-side Reverb application credentials and internal connection settings |
| `VITE_REVERB_*` | Browser-facing Reverb host, port, scheme, and public app key |
| `MAIL_*` | Mail transport; local development writes mail to the application log |
| `RECOMMENDATION_*` | Optional external course recommendation endpoint and timeouts |

Changing a `VITE_*` value requires rebuilding frontend assets.

### Realtime events

Reverb is the default broadcaster. The Compose environment uses `reverb:8080` for server-side communication and `localhost:8080` for the browser. If the application is accessed from another device or deployed behind a proxy, update the `VITE_REVERB_HOST`, port, and scheme, then rebuild assets.

### Recommendation service

Recommendations are disabled safely when `RECOMMENDATION_ENDPOINT` is empty. When enabled, RoadToSchool sends an `application/x-www-form-urlencoded` POST request containing:

```text
appId=<configured application id>
userId=<authenticated user id>
count=<maximum result count>
```

The service should return JSON keyed by the user ID, for example:

```json
{
  "3": [1, 2, 5]
}
```

Invalid IDs, duplicate IDs, timeouts, and unavailable recommendation services are handled without breaking the course page.

### YouTube integration

Instructors can provide standard YouTube watch, short, embed, Shorts, and live URLs. The application validates the host and video ID, retrieves title/description/duration metadata, and renders videos through the privacy-enhanced `youtube-nocookie.com` domain.

## Database and demo data

Apply pending migrations without deleting data:

```bash
docker compose -f compose.local.yaml exec -T app php artisan migrate
```

Create the demo data on a new database:

```bash
docker compose -f compose.local.yaml exec -T app php artisan db:seed
```

To deliberately rebuild the local database:

```bash
docker compose -f compose.local.yaml exec -T app php artisan migrate:fresh --seed
```

`migrate:fresh` deletes every table and all existing data. Use it only for a disposable local database.

## Project structure

```text
app/
  Events/                 Realtime broadcast events
  Http/Controllers/       Admin, instructor, user, and authentication flows
  Http/Middleware/        Authentication and permission enforcement
  Http/Requests/          Input validation and authorization
  Models/                 Eloquent domain models
  Services/               Recommendation and YouTube integrations
database/
  migrations/             Database schema history
  seeds/                  Core and local demo seeders
resources/
  js/                     Vite and compatibility JavaScript entries
  lang/en, lang/vi/       English and Vietnamese translations
  sass/                   Application and administrator styles
  views/                  Blade templates
routes/                   Web, API, channel, and console routes
tests/
  Feature, Unit/          PHPUnit coverage
  browser/                Playwright smoke journey
compose.local.yaml        Reproducible local services
Dockerfile.local          PHP 8.5 local runtime
```

## Security and compatibility notes

- User identity, prices, quiz totals, notification ownership, and redirect targets are derived on the server rather than trusted from client input.
- Course, lecture, cart, profile, notification, conversation, and quiz operations include ownership or enrollment checks.
- Uploaded images and YouTube URLs are validated before use; user-generated real-time content is escaped before rendering.
- Sensitive multi-record operations are transactional and designed to be idempotent where repeated requests are possible.
- jQuery remains on 3.7 because the preserved template plugins are not yet compatible with jQuery 4.
- Guzzle remains on the latest compatible 7.x release because Guzzle 8 requires PSR package majors that conflict with the current Reverb/Laravel dependency graph.

## Production deployment

`compose.local.yaml` and `Dockerfile.local` are intended for local development, not as a production deployment definition. A production deployment should at minimum:

1. Use production-only secrets and database credentials.
2. Set `APP_ENV=production`, `APP_DEBUG=false`, and the public `APP_URL`.
3. Install PHP dependencies with `composer install --no-dev --optimize-autoloader`.
4. Build assets with `npm ci --ignore-scripts && npm run build`.
5. Run `php artisan migrate --force` during a controlled release.
6. Cache configuration, routes, events, and views with `php artisan optimize`.
7. Serve Laravel through a production web server and process manager.
8. Run and supervise Reverb separately, with TLS termination for public WebSockets.
9. Configure a real mail transport, durable storage, backups, monitoring, and log retention.

The current queue driver is `sync`; introduce and supervise a queue backend before moving expensive work to background jobs.

## Development workflow

The modernization work is maintained on the `minhle` branch. Keep commits focused, run the relevant PHP/browser tests before pushing, and do not commit `.env`, `vendor`, `node_modules`, or generated `public/build` files.
