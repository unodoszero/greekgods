<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: ./login.php");
//     exit();
// }

$userId = current_user_id();

// Check if user ID is provided
if (isset($_POST['userId'])) {
    $userId = (int) $_POST['userId'];

    // Start transaction
    $pdo = db();
    $pdo->beginTransaction();

    try {
        // Delete workouts associated with the user
        $stmt = $pdo->prepare("DELETE FROM workouts WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);

        // Delete the program associated with the user
        $stmt = $pdo->prepare("DELETE FROM programs WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);

        // Commit transaction
        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Program and workouts deleted successfully']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error deleting program and workouts: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User ID not provided']);
}
?>
