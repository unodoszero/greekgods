# GreekGods

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/Database-PostgreSQL-4169E1?logo=postgresql&logoColor=white)
![Supabase](https://img.shields.io/badge/Backend-Supabase-3FCF8E?logo=supabase&logoColor=white)
![Vite](https://img.shields.io/badge/Build-Vite-646CFF?logo=vite&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Styles-Tailwind_CSS-06B6D4?logo=tailwindcss&logoColor=white)
![Vercel](https://img.shields.io/badge/Deploy-Vercel-000000?logo=vercel&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-yellow)

GreekGods is a Laravel fitness planning application for creating weekly workout programs, managing personal body metrics, and estimating training-related nutrition targets. The project began as a PHP fitness website and was migrated into a Laravel application with Blade views, session authentication, PostgreSQL persistence, OAuth login, and Vercel deployment support.

## Table of Contents

- [Preview](#preview)
- [Tech Stack](#tech-stack)
- [Repository Overview](#repository-overview)
- [Features](#features)
- [Installation Guide](#installation-guide)
- [Usage](#usage)
- [Migration and Domain Logic](#migration-and-domain-logic)
- [Constraints and Future Improvements](#constraints-and-future-improvements)
- [License](#license)

## Preview

| Preview | Description |
| --- | --- |
| <img src="public/preview/Screenshot%202026-07-03%20at%204.28.35%E2%80%AFPM.png" alt="GreekGods home page" width="360"> | Home page with authenticated navigation and a monochrome fitness hero visual. |
| <img src="public/preview/FireShot%20Capture%20018%20-%20GreekGods%20-%20Profile%20-%20%5B127.0.0.1%5D.png" alt="GreekGods profile dashboard" width="360"> | Profile dashboard showing saved body metrics, BMI, calorie targets, and today's workout. |
| <img src="public/preview/Screenshot%202026-07-03%20at%204.31.06%E2%80%AFPM.png" alt="GreekGods weekly workout board" width="360"> | Program builder with split selection, weekly schedule mapping, rest-day locking, and editable workouts. |

## Tech Stack

| Category | Technology | Use |
| --- | --- | --- |
| Language | PHP 8.3 | Main server-side language for the Laravel application and migration scripts. |
| Framework | Laravel 13 | Routing, controllers, requests, validation, sessions, Eloquent models, migrations, and tests. |
| Frontend | Blade | Server-rendered views for authentication, profile, program, and page layouts. |
| Frontend | JavaScript | Interactive workout program behavior, profile updates, and legacy page interactions. |
| Styling | CSS, Tailwind CSS 4, Trebuchet MS assets | Custom application styling, Vite-managed CSS, and bundled typography assets. |
| Database | PostgreSQL | Primary relational database used by Laravel migrations and the Supabase deployment target. |
| Backend service | Supabase | Hosted PostgreSQL database for production-style deployment. |
| Authentication | Laravel session auth | Email/password registration, login, logout, and protected profile/program routes. |
| OAuth | Laravel Socialite, SocialiteProviders Microsoft | Google and Microsoft sign-in, account linking, avatar capture, and profile completion flow. |
| Notifications | masmerise/livewire-toaster | Toast messages for authentication and account actions. |
| Build tools | Composer, npm, Vite, Laravel Vite plugin | PHP dependency management, JavaScript/CSS build pipeline, and asset bundling. |
| Testing | PHPUnit | Feature and unit test runner through `php artisan test`. |
| Deployment | Vercel PHP runtime | Vercel configuration routes all application requests through `api/index.php` and serves built/static assets from `public`. |
| Data migration | Custom PHP migration script | Moves legacy MySQL user, program, and workout data into PostgreSQL tables. |

## Repository Overview

| Path | Purpose |
| --- | --- |
| `app/Http/Controllers` | Laravel controllers for pages, authentication, social login, profile management, and workout programs. |
| `app/Http/Requests` | Form request validation for registration, social profile completion, profile updates, and password changes. |
| `app/Models` | Eloquent models for users, programs, and workouts. |
| `app/Support` | Domain helpers for body metric conversion, workout split definitions, social provider metadata, and session identity state. |
| `config/preconfigured_workouts.php` | Exercise templates used when a user saves a predefined workout split. |
| `database/migrations` | Laravel schema migrations for users, programs, workouts, cache, jobs, social auth fields, and profile metrics. |
| `resources/views` | Blade templates for the app layout, auth screens, profile dashboard, program builder, and vendor toast components. |
| `resources/js` and `resources/css` | Vite-managed frontend entry points and bundled vendor toaster scripts. |
| `public` | Public entry point, compiled/static assets, legacy CSS/JS assets, icons, fonts, and preview images. |
| `files` and `articles` | Legacy PHP/static pages retained while routes redirect or render their content through Laravel. |
| `scripts/migrate_mysql_to_postgres.php` | One-off migration utility for moving legacy MySQL data into PostgreSQL. |
| `vercel.json` and `api/index.php` | Vercel deployment configuration for running Laravel with a PHP serverless runtime. |

## Features

- Email/password registration and login with Laravel session authentication.
- Google and Microsoft OAuth login with account linking and a profile-completion step for new social signups.
- Profile dashboard with height, weight, age, activity level, sex, BMI, BMR, TDEE, calorie targets, and protein estimates.
- Metric conversion for height and weight across `cm`, `m`, `in`, `ft`, `kg`, and `lb`.
- Workout split catalog covering full body, upper/lower, PPL, PPL/Upper/Lower, PPL/Upper/ShArms, Arnold, and body-part splits.
- Weekly program builder with schedule selection, preconfigured workout templates, editable workouts, and rest-day restrictions.
- Legacy URL redirects for previous `/files/*.php`, `/files/*.html`, `/articles/*.php`, and `index.php` routes.
- Supabase PostgreSQL configuration and Vercel deployment support.

## Installation Guide

### Prerequisites

- PHP 8.3 or newer
- Composer
- Node.js and npm
- PostgreSQL database, preferably a Supabase project if matching the deployment setup

### Setup

1. Install PHP dependencies.

   ```sh
   composer install
   ```

2. Install JavaScript dependencies.

   ```sh
   npm install
   ```

3. Create the environment file and generate an app key.

   ```sh
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure the PostgreSQL connection in `.env`.

   ```sh
   DB_CONNECTION=pgsql
   DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
   DB_PORT=6543
   DB_DATABASE=postgres
   DB_USERNAME=postgres.<project-ref>
   DB_PASSWORD=<database-password>
   DB_SSLMODE=require
   DB_EMULATE_PREPARES=true
   ```

5. Run the migrations.

   ```sh
   php artisan migrate
   ```

6. Build frontend assets for a production-style run.

   ```sh
   npm run build
   ```

## Usage

Start the Laravel server:

```sh
php artisan serve
```

For active frontend development, run Vite in a separate terminal:

```sh
npm run dev
```

The Composer `dev` script can run the Laravel server, queue listener, logs, and Vite together:

```sh
composer run dev
```

Run the test suite:

```sh
php artisan test
```

### Social Login

Google and Microsoft login buttons are available when provider credentials are configured. Add the credentials to `.env`, then clear Laravel's cached config:

```sh
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

MICROSOFT_CLIENT_ID=
MICROSOFT_CLIENT_SECRET=
MICROSOFT_REDIRECT_URI="${APP_URL}/auth/microsoft/callback"
MICROSOFT_TENANT_ID=common
MICROSOFT_INCLUDE_AVATAR=true
MICROSOFT_AVATAR_SIZE=648x648
```

```sh
php artisan config:clear
```

### Vercel Deployment

The repository includes `vercel.json` for routing static assets from `public` and application requests through `api/index.php`.

Recommended Vercel settings:

```sh
Framework Preset: Other
Build Command: npm run build
```

Set production environment variables in Vercel rather than committing secrets. For database-backed sessions on Vercel, use:

```sh
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
VIEW_COMPILED_PATH=/tmp/views
```

Run production migrations once after configuring the deployment database:

```sh
php artisan migrate --force
```

## Migration and Domain Logic

This repository is notable for preserving an older PHP/static site while moving the application core into Laravel. Legacy routes such as `/files/login.php`, `/files/program.php`, `/articles/{slug}.php`, and `/index.php` are redirected into the Laravel route layer so older links continue to resolve.

The fitness logic is also centralized instead of being scattered through page scripts. `WorkoutSplitCatalog` defines available splits, valid schedules, training days, rest days, and legacy aliases. `BodyMetricConverter` normalizes user-entered height and weight into meters and kilograms so BMI, BMR, TDEE, calorie targets, and protein estimates can be calculated consistently.

## Constraints and Future Improvements

- Some legacy PHP, CSS, and JavaScript assets remain in the repository while the Laravel migration is in progress.
- The preview images are stored as static screenshots rather than generated from an automated visual test workflow.
- Workout recommendations are rule/template based; future versions could support progression history, set completion tracking, and analytics.
- The current database target is PostgreSQL/Supabase; local SQLite setup is not the primary documented path.
- Additional tests would improve confidence around social auth edge cases, profile metric validation, and workout schedule constraints.
- CI/CD could be added to run tests, formatting, and build checks before deployment.

## License

This project is licensed under the [MIT License](LICENSE).
