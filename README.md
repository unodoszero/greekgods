# Greekgods 

<img src="graphics/logo/logo.png" alt="Greekgods logo" width="100"/>

A beginner-friendly fitness website designed to help you plan weekly workouts, track body metrics, and guide you through your fitness journey with confidence.

---

## Laravel setup

The app now runs on Laravel with Blade views, Laravel session auth, and Supabase PostgreSQL.

Install dependencies:

```sh
composer install
npm install
```

Create a local environment file and app key:

```sh
cp .env.example .env
php artisan key:generate
```

Start the Laravel development server:

```sh
php artisan serve
```

## Supabase database setup

Fill in the Supabase connection details in `.env` from Project Settings > Database.

Recommended Supabase values:

- database: `postgres`
- user: `postgres.<project-ref>`
- host: your Supabase pooler host, for example `aws-0-ap-southeast-1.pooler.supabase.com`
- port: `6543`
- sslmode: `require`

Example `.env` values:

```sh
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.<project-ref>
DB_PASSWORD=<password>
DB_SSLMODE=require
```

Run Laravel migrations on a fresh Supabase database:

```sh
php artisan migrate
```

## Vercel deployment

This repository includes Vercel config for running Laravel through Vercel's PHP community runtime and serving static assets from `public`.

In Vercel, use these project settings:

```sh
Framework Preset: Other
Build Command: npm run build
```

Add these environment variables in Vercel. Do not commit real secrets.

```sh
APP_NAME=Greekgods
APP_ENV=production
APP_KEY=<output of php artisan key:generate --show>
APP_DEBUG=false
APP_URL=https://your-vercel-domain.vercel.app

DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.<project-ref>
DB_PASSWORD=<password>
DB_SSLMODE=require
DB_EMULATE_PREPARES=true

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
VIEW_COMPILED_PATH=/tmp/views
```

After the Supabase variables are set locally, run production migrations once:

```sh
php artisan migrate --force
```

For Google or Microsoft login, create OAuth redirect URLs for the deployed domain:

```sh
https://your-vercel-domain.vercel.app/auth/google/callback
https://your-vercel-domain.vercel.app/auth/microsoft/callback
```

## Social login setup

The app supports email/password login plus Google and Microsoft OAuth login through Laravel Socialite.

Install dependencies:

```sh
composer install
```

Create OAuth applications with these redirect URLs:

- Google: `${APP_URL}/auth/google/callback`
- Microsoft: `${APP_URL}/auth/microsoft/callback`

Add the provider credentials to `.env`:

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

Then clear cached config so Laravel reads the new credentials:

```sh
php artisan config:clear
```

If either provider is missing its client ID, client secret, or redirect URI, that social button is disabled and the redirect route returns a local error instead of sending users to Google or Microsoft with an invalid request.

New social signups are sent through a short profile-completion form because Google and Microsoft do not provide the required GreekGods fitness fields.

Run tests:

```sh
php artisan test
```
