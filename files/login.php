<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

// Initialize error message variable
$error_message = "";

// Step 2: Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 3: Retrieve form inputs
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Step 6: Check if a matching record is found
    if ($user && password_verify($password, $user['password_hash'])) {
        $userId = (int) $user['id'];
        $_SESSION['user_id'] = $userId;

        // Redirect to the profile page with user_id as a query parameter
        header("Location: ./profile.php");
        exit();
    } else {
        // No matching user found, set error message
        $error_message = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../graphics/logo/logo.png">
    <link rel="stylesheet" href="login.css">
    <title>GreekGods | Login</title>
</head>
<body>
    <div class="container">
        <form id="login-form" action="login.php" method="POST">
            <img src="../graphics/logo/logo.png" onclick="location.href='../index.php'" alt="Logo" title="Click here to redirect to home">
            <p>Login</p>
            <p id="description">Ready to power up your fitness journey? We're excited to see you back—let’s keep reaching those goals together!</p>
        
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="e.g. ada.lovelace@icloud.com" required>
        
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        
            <!-- Error message container, display error from PHP -->
            <div class="error-message-container">
                <?php
                // Display the error message if it exists
                if (!empty($error_message)) {
                    echo '<p class="error-message">' . htmlspecialchars($error_message) . '</p>';
                }
                ?>
            </div>
        
            <button type="submit">Login</button>
            <hr>
            <p>New to GreekGods? Create an account to start your fitness journey with us! <a href="register.html">Register</a></p>
        </form>        
    </div>
    <script src="login.js"></script>
</body>
</html>
