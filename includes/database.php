<?php
declare(strict_types=1);

function database_config(): array
{
    $localConfigPath = __DIR__ . '/database.local.php';
    $localConfig = is_file($localConfigPath) ? require $localConfigPath : [];

    $config = array_replace_recursive([
        'pgsql' => [
            'host' => '127.0.0.1',
            'port' => '5432',
            'database' => 'greekgods',
            'username' => 'greekgods_app',
            'password' => '',
        ],
        'mysql' => [
            'host' => getenv('MYSQL_HOST') ?: '',
            'port' => getenv('MYSQL_PORT') ?: '3306',
            'database' => getenv('MYSQL_DATABASE') ?: '',
            'username' => getenv('MYSQL_USER') ?: '',
            'password' => getenv('MYSQL_PASSWORD') ?: '',
        ],
    ], is_array($localConfig) ? $localConfig : [], database_environment_config());

    $isSupabase = is_supabase_connection((string) $config['pgsql']['host'], (string) $config['pgsql']['port']);
    $config['pgsql']['sslmode'] = $config['pgsql']['sslmode'] ?? ($isSupabase ? 'require' : 'prefer');
    $config['pgsql']['emulate_prepares'] = $config['pgsql']['emulate_prepares'] ?? $isSupabase;

    return $config;
}

function database_environment_config(): array
{
    $databaseUrlConfig = parse_postgres_url(getenv('SUPABASE_DATABASE_URL') ?: getenv('DATABASE_URL') ?: '');
    $pgsql = array_filter([
        'host' => getenv('SUPABASE_DB_HOST') ?: getenv('PGHOST') ?: null,
        'port' => getenv('SUPABASE_DB_PORT') ?: getenv('PGPORT') ?: null,
        'database' => getenv('SUPABASE_DB_NAME') ?: getenv('PGDATABASE') ?: null,
        'username' => getenv('SUPABASE_DB_USER') ?: getenv('PGUSER') ?: null,
        'password' => getenv('SUPABASE_DB_PASSWORD') ?: getenv('PGPASSWORD') ?: null,
        'sslmode' => getenv('SUPABASE_DB_SSLMODE') ?: getenv('PGSSLMODE') ?: null,
    ], static function ($value): bool {
        return $value !== null;
    });

    if (getenv('PG_EMULATE_PREPARES') !== false) {
        $pgsql['emulate_prepares'] = filter_var(getenv('PG_EMULATE_PREPARES'), FILTER_VALIDATE_BOOL);
    }

    $pgsql = array_replace($pgsql, $databaseUrlConfig);

    return $pgsql === [] ? [] : ['pgsql' => $pgsql];
}

function parse_postgres_url(string $databaseUrl): array
{
    if ($databaseUrl === '') {
        return [];
    }

    $parts = parse_url($databaseUrl);
    if (!is_array($parts)) {
        return [];
    }

    $config = [];

    if (isset($parts['host'])) {
        $config['host'] = $parts['host'];
    }

    if (isset($parts['port'])) {
        $config['port'] = (string) $parts['port'];
    }

    if (isset($parts['path'])) {
        $database = ltrim($parts['path'], '/');
        if ($database !== '') {
            $config['database'] = $database;
        }
    }

    if (isset($parts['user'])) {
        $config['username'] = rawurldecode($parts['user']);
    }

    if (isset($parts['pass'])) {
        $config['password'] = rawurldecode($parts['pass']);
    }

    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
        if (isset($query['sslmode']) && is_string($query['sslmode'])) {
            $config['sslmode'] = $query['sslmode'];
        }
    }

    return $config;
}

function is_supabase_connection(string $host, string $port): bool
{
    return strpos($host, 'supabase.co') !== false || $port === '6543';
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = database_config()['pgsql'];
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['sslmode']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => (bool) $config['emulate_prepares'],
    ]);

    return $pdo;
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function fetch_user_name(?int $userId): array
{
    if (!$userId) {
        return ['firstName' => '', 'lastName' => ''];
    }

    $stmt = db()->prepare('SELECT first_name, last_name FROM users WHERE id = :user_id');
    $stmt->execute(['user_id' => $userId]);
    $user = $stmt->fetch();

    return [
        'firstName' => $user['first_name'] ?? '',
        'lastName' => $user['last_name'] ?? '',
    ];
}
