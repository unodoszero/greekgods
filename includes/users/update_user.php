<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../database.php';

$userId = current_user_id();
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data provided.']);
    exit();
}

// Update user data
$params = [
    'email' => $data['email'],
    'first_name' => $data['firstName'],
    'last_name' => $data['lastName'],
    'birthdate' => $data['birthdate'],
    'height' => $data['height'],
    'weight' => $data['weight'],
    'activity' => $data['activity'],
    'user_id' => $userId,
];

$passwordSql = '';
if (!empty($data['password'])) {
    $passwordSql = ', password_hash = :password_hash';
    $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
}

$stmt = db()->prepare("
    UPDATE users
    SET email = :email,
        first_name = :first_name,
        last_name = :last_name,
        birthdate = :birthdate,
        height = :height,
        weight = :weight,
        activity = :activity
        $passwordSql
    WHERE id = :user_id
");

if ($stmt->execute($params)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update data.']);
}
?>
