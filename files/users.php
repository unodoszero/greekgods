<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

// Retrieve email and password from POST data
$email = $_POST['email'] ?? null;
$plainPassword = $_POST['password'] ?? null;
$firstName = $_POST['first-name'] ?? null;
$lastName = $_POST['last-name'] ?? null;
$birthdate = $_POST['birthdate'] ?? null;
$height = $_POST['height'] ?? null;
$weight = $_POST['weight'] ?? null;
$activity = $_POST['activity'] ?? null;

// Validate required fields (ensure all fields are filled)
if (!$email || !$plainPassword || !$firstName || !$lastName || !$birthdate || !$height || !$weight || !$activity) {
    die("Error: Missing required fields.");
}

$stmt = db()->prepare("
    INSERT INTO users (email, password_hash, first_name, last_name, birthdate, height, weight, activity)
    VALUES (:email, :password_hash, :first_name, :last_name, :birthdate, :height, :weight, :activity)
    RETURNING id
");

// Execute the query to insert the data
$stmt->execute([
    'email' => $email,
    'password_hash' => password_hash($plainPassword, PASSWORD_DEFAULT),
    'first_name' => $firstName,
    'last_name' => $lastName,
    'birthdate' => $birthdate,
    'height' => $height,
    'weight' => $weight,
    'activity' => $activity,
]);
$userId = (int) $stmt->fetchColumn();

if ($userId) {
    // Get the user_id of the last inserted record
    $_SESSION['user_id'] = $userId;

    // Redirect to profile.php with user_id in the query 'string
    header("Location: ./profile.php");
    exit;
} else {
    echo "Error: Failed to create account.";
}
?>
