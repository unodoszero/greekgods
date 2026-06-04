<?php
session_start();
require_once __DIR__ . "/../includes/database.php";

$userId = current_user_id();
$userName = fetch_user_name($userId);
$firstName = $userName["firstName"];
$lastName = $userName["lastName"];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../graphics/logo/logo.png">
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="./blog.css">
    <script type="text/javascript">
        const userId = <?php echo json_encode($userId); ?>;
    </script>
    <title>GreekGods | Blog</title>
</head>
<body>
    <nav>
        <button class="nav-menu-button" id="nav-menu-button">
            <img src="../graphics/svg/menu-black.svg" alt="Menu" title="Menu">
        </button>
        <div class="nav-logo">
            <img src="../graphics/logo/greekgodslogo.png" alt="GreekGods" title="GreekGods" onclick="window.location.href='../index.php'">
        </div>
        <button class="nav-menu-profile" id="nav-menu-profile" onclick="window.location.href='./register.html'">
            <img src="../graphics/svg/profile.svg" alt="Profile" title="Profile">
        </button>
        <ul class="nav-links" id="nav-links">
            <li><a href="../index.php">HOME</a></li>
            <li><a href="./program.php">PROGRAM</a></li>
            <li><a href="./blog.php" onclick="location.reload(); return false;">BLOG</a></li>
            <li><a href="./calculator.php">CALCULATOR</a></li>
            <li><a href="./about.php">ABOUT</a></li>
        </ul>
        <div class="nav-button">
            <button id="register-button" onclick="window.location.href='./register.html'">GET STARTED</button>
            <button id="profile-button" onclick="window.location.href='./profile.php'"><img src="../graphics/svg/profile.svg" alt="Profile" title="Profile"></button>
            <span id="profile-name"><?php echo htmlspecialchars($firstName . " " . $lastName); ?></span>
        </div>
    </nav>
    <header>
        <div class="header-container">
            <h3>EVERY JOURNEY BEGINS HERE</h3>
            <p>Welcome to the GreekGods Blog — your ultimate destination for fitness knowledge and inspiration! Whether you're just starting out, leveling up, or pushing for god-like gains, we've got you covered. Dive into expertly crafted articles tailored to your fitness journey.</p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-beginner">
                <h3>Beginner</h3>
                <div class="beginner-articles">
                    <article onclick="window.location.href='../articles/bmr.php'">
                        <img src="../graphics/images/bmr.webp" alt="Basal Metabolic Rate" title="Basal Metabolic Rate">
                        <h4>What is Basal Metabolic Rate (BMR)</h4>
                        <p>Explore how BMR impacts your energy needs, weight management, and overall health. Understand how to calculate it and why it matters for your fitness journey.</p>
                    </article>
                    <article onclick="window.location.href='../articles/bmi.php'">
                        <img src="../graphics/images/bmi.jpg" alt="Body Mass Index" title="Body Mass Index">
                        <h4>Introduction to Body Mass Index (BMI)</h4>
                        <p>Learn how BMI is used to categorize body weight and assess health risks. A crucial starting point for understanding your fitness goals.</p>
                    </article>
                    <article onclick="window.location.href='../articles/workout-splits.php'">
                        <img src="../graphics/images/workout-splits.webp" alt="Workout Splits" title="Workout Splits">
                        <h4>Workout Split</h4>
                        <p>Find out how to structure your workouts effectively for maximum results. Learn about the different types of workout splits and how to implement them in your routine.</p>
                    </article>
                    <article onclick="window.location.href='../articles/calorie-deficit.php'">
                        <img src="../graphics/images/calorie-deficit.jpeg" alt="Calorie Deficit" title="Calorie Deficit">
                        <h4>Calorie Deficit</h4>
                        <p>Learn how to calculate and maintain a calorie deficit for weight loss. Understand the role of nutrition and exercise in achieving your weight goals.</p>
                    </article>
                    <article onclick="window.location.href='../articles/protein.php'">
                        <img src="../graphics/images/protein.jpg" alt="Protein for Muscle Growth" title="Protein for Muscle Growth">
                        <h4>Protein</h4>
                        <p>Master the art of protein intake to support muscle growth, repair, and overall fitness. Understand the best sources of protein for your fitness needs.</p>
                    </article>
                    <article onclick="window.location.href='../articles/ppl.php'">
                        <img src="../graphics/images/ppl.jpg" alt="Push, Pull, Legs Split" title="Push, Pull, Legs Split">
                        <h4>Push, Pull, and Leg</h4>
                        <p>Learn how the popular Push, Pull, Legs split can organize your workouts for maximum efficiency. Ideal for muscle growth and recovery.</p>
                    </article>
                </div>
            </div>
            <div class="main-all">
                <h3>Advanced</h3>
                <div class="all-articles">
                    <article onclick="window.location.href='../articles/progressive-overload.php'">
                        <img src="../graphics/images/progressive-overload.jpeg" alt="Progressive Overload" title="Progressive Overload">
                        <h4>Progressive Overload</h4>
                        <p>Understand how gradually increasing the intensity of your workouts can help you build strength and muscle, ensuring continuous progress.</p>
                    </article>
                    <article onclick="window.location.href='../articles/muscles.php'">
                        <img src="../graphics/images/muscles.jpg" alt="Types of Muscles" title="Types of Muscles">
                        <h4>Muscles</h4>
                        <p>Dive deeper into the different types of muscles in your body and learn how each plays a role in your fitness routine and overall performance.</p>
                    </article>
                    <article onclick="window.location.href='../articles/advance-splits.php'">
                        <img src="../graphics/images/advance-splits.jpg" alt="Advanced Workout Splits" title="Advanced Workout Splits">
                        <h4>Advance Workout Splits</h4>
                        <p>Take your training to the next level with advanced workout splits that help you target specific muscle groups and optimize recovery for faster gains.</p>
                    </article>
                    <article onclick="window.location.href='../articles/bmr-vs-tdee.php'">
                        <img src="../graphics/images/bmr-vs-tdee.webp" alt="BMR vs TDEE" title="BMR vs TDEE">
                        <h4>BMR vs. TDEE</h4>
                        <p>After mastering BMR, learn the importance of Total Daily Energy Expenditure (TDEE) and how it helps in determining calorie requirements for fat loss or muscle gain.</p>
                    </article>
                    <article onclick="window.location.href='../articles/isolation-vs-composite.php'">
                        <img src="../graphics/images/isolation-vs-composite.webp" alt="Isolation vs Composite Exercises" title="Isolation vs Composite Exercises">
                        <h4>Isolation vs. Composite</h4>
                        <p>Discover the key differences between isolation and composite exercises, and how each can be strategically included in your training for balanced muscle development.</p>
                    </article>
                    <article onclick="window.location.href='../articles/supplements.php'">
                        <img src="../graphics/images/supplements.jpg" alt="Fitness Supplements" title="Fitness Supplements">
                        <h4>Supplements</h4>
                        <p>Unlock your full potential by learning about the essential supplements that can enhance your performance, recovery, and overall health. Discover which ones fit into your fitness routine!</p>
                    </article>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="footer-container">
            <ul class="footer-links">
                <li><a href="../index.php">HOME</a></li>
                <li><a href="./blog.php">BLOG</a></li>
                <li><a href="./about.php">ABOUT</a></li>
                <li><a href="./laws.html">DISCLAIMER</a></li>
                <li><a href="./about.php">CONTACT</a></li>
                <li><a href="./laws.html">PRIVACY POLICY</a></li>
                <li><a href="./laws.html">TERMS OF USE</a></li>
            </ul>
            <div class="footer-socials">
                <a href="https://www.facebook.com" target="_blank">
                    <img src="../graphics/socials/facebook.png" alt="Facebook" title="Facebook">
                </a>
                <a href="https://www.instagram.com" target="_blank">
                    <img src="../graphics/socials/instagram.png" alt="Instagram" title="Instagram">
                </a>
                <a href="https://www.twitter.com" target="_blank">
                    <img src="../graphics/socials/twitter.png" alt="Twitter" title="Twitter">
                </a>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2024 GreekGods. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="../index.js"></script>
</body>
</html>