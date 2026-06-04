<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../database.php';

$userId = current_user_id();
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

// Fetch user data
$stmt = db()->prepare("
    SELECT
        email,
        first_name AS \"firstName\",
        last_name AS \"lastName\",
        birthdate,
        height,
        weight,
        activity
    FROM users
    WHERE id = :user_id
");
$stmt->execute(['user_id' => $userId]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}
?>
