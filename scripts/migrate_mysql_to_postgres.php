<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/database.php';

function mysql_db(): PDO
{
    $config = database_config()['mysql'];

    foreach (['host', 'database', 'username'] as $key) {
        if (empty($config[$key])) {
            fwrite(STDERR, "Missing MySQL config value: {$key}\n");
            exit(1);
        }
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['host'],
        $config['port'],
        $config['database']
    );

    return new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function count_table(PDO $pdo, string $table): int
{
    return (int) $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
}

try {
    $mysql = mysql_db();
    $pgsql = db();
} catch (Throwable $e) {
    fwrite(STDERR, "Database connection failed: {$e->getMessage()}\n");
    exit(1);
}
$schemaSql = file_get_contents(__DIR__ . '/../migrations/postgres_schema.sql');

if ($schemaSql === false) {
    fwrite(STDERR, "Unable to read PostgreSQL schema file.\n");
    exit(1);
}

$sourceCounts = [
    'users' => count_table($mysql, 'users'),
    'program' => count_table($mysql, 'program'),
    'workouts' => count_table($mysql, 'workouts'),
];

$pgsql->beginTransaction();

try {
    $pgsql->exec($schemaSql);
    $pgsql->exec('TRUNCATE TABLE workouts, programs, users RESTART IDENTITY CASCADE');

    $insertUser = $pgsql->prepare('
        INSERT INTO users (id, email, password_hash, first_name, last_name, birthdate, height, weight, activity)
        VALUES (:id, :email, :password_hash, :first_name, :last_name, :birthdate, :height, :weight, :activity)
    ');

    $users = $mysql->query('
        SELECT user_id, email, password, firstName, lastName, birthdate, height, weight, activity
        FROM users
        ORDER BY user_id
    ');

    foreach ($users as $user) {
        $insertUser->execute([
            'id' => (int) $user['user_id'],
            'email' => $user['email'],
            'password_hash' => password_hash((string) $user['password'], PASSWORD_DEFAULT),
            'first_name' => $user['firstName'],
            'last_name' => $user['lastName'],
            'birthdate' => $user['birthdate'],
            'height' => $user['height'],
            'weight' => $user['weight'],
            'activity' => $user['activity'],
        ]);
    }

    $insertProgram = $pgsql->prepare('
        INSERT INTO programs (user_id, program, schedule)
        VALUES (:user_id, :program, :schedule)
    ');

    $programs = $mysql->query('
        SELECT user_id, program, schedule
        FROM program
        ORDER BY user_id
    ');

    foreach ($programs as $program) {
        $insertProgram->execute([
            'user_id' => (int) $program['user_id'],
            'program' => $program['program'],
            'schedule' => $program['schedule'],
        ]);
    }

    $insertWorkout = $pgsql->prepare('
        INSERT INTO workouts (user_id, workout_name, workout_reps, workout_sets, workout_day)
        VALUES (:user_id, :workout_name, :workout_reps, :workout_sets, :workout_day)
    ');

    $workouts = $mysql->query('
        SELECT user_id, workoutName, workoutReps, workoutSets, workoutDay
        FROM workouts
        ORDER BY user_id, workoutDay, workoutName
    ');

    foreach ($workouts as $workout) {
        $insertWorkout->execute([
            'user_id' => (int) $workout['user_id'],
            'workout_name' => $workout['workoutName'],
            'workout_reps' => (int) $workout['workoutReps'],
            'workout_sets' => (int) $workout['workoutSets'],
            'workout_day' => $workout['workoutDay'],
        ]);
    }

    $pgsql->exec("SELECT setval(pg_get_serial_sequence('users', 'id'), COALESCE((SELECT MAX(id) FROM users), 1), (SELECT COUNT(*) > 0 FROM users))");
    $pgsql->exec("SELECT setval(pg_get_serial_sequence('programs', 'id'), COALESCE((SELECT MAX(id) FROM programs), 1), (SELECT COUNT(*) > 0 FROM programs))");
    $pgsql->exec("SELECT setval(pg_get_serial_sequence('workouts', 'id'), COALESCE((SELECT MAX(id) FROM workouts), 1), (SELECT COUNT(*) > 0 FROM workouts))");

    $pgsql->commit();
} catch (Throwable $e) {
    $pgsql->rollBack();
    fwrite(STDERR, "Migration failed: {$e->getMessage()}\n");
    exit(1);
}

$targetCounts = [
    'users' => count_table($pgsql, 'users'),
    'programs' => count_table($pgsql, 'programs'),
    'workouts' => count_table($pgsql, 'workouts'),
];

echo "Migration complete.\n";
echo "MySQL users: {$sourceCounts['users']} -> PostgreSQL users: {$targetCounts['users']}\n";
echo "MySQL program: {$sourceCounts['program']} -> PostgreSQL programs: {$targetCounts['programs']}\n";
echo "MySQL workouts: {$sourceCounts['workouts']} -> PostgreSQL workouts: {$targetCounts['workouts']}\n";
