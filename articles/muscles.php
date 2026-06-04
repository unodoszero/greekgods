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
    <title>GreekGods | Muscles</title>
    <link rel="icon" type="image/x-icon" href="../graphics/logo/logo.png">
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="./article.css">
    <script type="text/javascript">
        const userId = <?php echo json_encode($userId); ?>;
    </script>
</head>
<body>
    <nav>
        <button class="nav-menu-button" id="nav-menu-button">
            <img src="../graphics/svg/menu-black.svg" alt="Menu" title="Menu">
        </button>
        <div class="nav-logo">
            <img src="../graphics/logo/greekgodslogo.png" alt="GreekGods" title="GreekGods" onclick="window.location.href='../index.php'">
        </div>
        <button class="nav-menu-profile" id="nav-menu-profile" onclick="window.location.href='../files/register.html'">
            <img src="../graphics/svg/profile.svg" alt="Profile" title="Profile">
        </button>
        <ul class="nav-links" id="nav-links">
            <li><a href="../index.php">HOME</a></li>
            <li><a href="../files/program.php">PROGRAM</a></li>
            <li><a href="../files/blog.php">BLOG</a></li>
            <li><a href="../files/calculator.php">CALCULATOR</a></li>
            <li><a href="../files/about.php">ABOUT</a></li>
        </ul>
        <div class="nav-button">
            <button id="register-button" onclick="window.location.href='../files/register.html'">GET STARTED</button>
            <button id="profile-button" onclick="window.location.href='../files/profile.php'"><img src="../graphics/svg/profile.svg" alt="Profile" title="Profile"></button>
            <span id="profile-name"><?php echo htmlspecialchars($firstName . " " . $lastName); ?></span>
        </div>
    </nav>
    <header>
        <div class="header-container">
            <h2>What You Need to Know About Building Muscles</h2>
            <p>Building muscle requires consistent effort, proper nutrition, and the right training techniques to stimulate growth and strength over time.</p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>Understanding the Muscles</h3>
                <p>The human body contains about 600 muscles with various functions, including pumping blood, aiding movement, and supporting physical activities. Muscles work by contracting and relaxing to create movement.</p>
                <p>There are three types of muscles in your body:</p>
                <ul>
                    <li><strong>Cardiac Muscles:</strong> Control the heart and pump blood.</li>
                    <li><strong>Smooth Muscles:</strong> Control involuntary functions like blood vessel constriction and digestion.</li>
                    <li><strong>Skeletal Muscles:</strong> The muscles you target in the gym that help with voluntary movement.</li>
                </ul>
                <p>The primary muscle groups include:</p>
                <ul>
                    <li>Chest</li>
                    <li>Back</li>
                    <li>Arms</li>
                    <li>Abdominals</li>
                    <li>Legs</li>
                    <li>Shoulders</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Working Multiple Muscles</h3>
                <p>Few exercises isolate only one muscle group. For instance, biceps curls also engage other muscles like the brachialis and brachioradialis. Core and stabilizer muscles help maintain proper form during these lifts. Exercises involving multiple joints engage more muscle groups.</p>
            </div>
            <div class="main-info">
                <h3>What to Pair Together?</h3>
                <p>One effective way to structure your training is to split muscle groups across different days to ensure proper rest and recovery:</p>
                <ul>
                    <li><strong>Chest, Shoulders, and Triceps:</strong> Exercises like bench presses, overhead presses, and triceps dips target pushing muscles.</li>
                    <li><strong>Back and Biceps:</strong> Focus on pulling muscles with exercises like rows, pull-ups, and bicep curls.</li>
                    <li><strong>Legs and Abs:</strong> Incorporate lower-body exercises like squats and lunges, along with core exercises like planks.</li>
                </ul>
                <p>This approach gives each muscle group time to recover while maintaining intensity in your workouts.</p>
            </div>
        </div>
    </main>
    <footer>
        <div class="footer-container">
            <ul class="footer-links">
                <li><a href="../index.php">HOME</a></li>
                <li><a href="../files/blog.php">BLOG</a></li>
                <li><a href="../files/about.php">ABOUT</a></li>
                <li><a href="../files/laws.html">DISCLAIMER</a></li>
                <li><a href="../files/about.php">CONTACT</a></li>
                <li><a href="../files/laws.html">PRIVACY POLICY</a></li>
                <li><a href="../files/laws.html">TERMS OF USE</a></li>
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
