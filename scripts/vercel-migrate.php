<?php

declare(strict_types=1);

$environment = trim((string) getenv('VERCEL_ENV'));
$migrationsEnabled = filter_var(
    getenv('RUN_DATABASE_MIGRATIONS') ?: 'false',
    FILTER_VALIDATE_BOOL,
);

if ($environment !== 'production') {
    fwrite(STDOUT, "Skipping database migrations outside Vercel production.\n");

    exit(0);
}

if (! $migrationsEnabled) {
    fwrite(STDOUT, "Skipping database migrations because RUN_DATABASE_MIGRATIONS is disabled.\n");

    exit(0);
}

if (trim((string) getenv('DB_CONNECTION')) === '') {
    fwrite(STDERR, "DB_CONNECTION must be configured before production migrations can run.\n");

    exit(1);
}

$artisan = dirname(__DIR__).'/artisan';
$command = sprintf(
    '%s %s migrate --force --isolated --no-interaction',
    escapeshellarg(PHP_BINARY),
    escapeshellarg($artisan),
);

fwrite(STDOUT, "Applying pending production database migrations.\n");
passthru($command, $exitCode);

exit($exitCode);
