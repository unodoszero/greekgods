<?php
session_start();
require_once __DIR__ . "/includes/database.php";

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
    <link rel="icon" type="image/x-icon" href="./graphics/logo/logo.png">
    <link rel="stylesheet" href="./index.css">
    <script type="text/javascript">
        const userId = <?php echo json_encode($userId); ?>;
    </script>
    <title>GreekGods | Home</title>
</head>
<body>
    <div class="container">
        <nav>
            <button class="nav-menu-button" id="nav-menu-button">
                <img src="./graphics/svg/menu-black.svg" alt="Menu" title="Menu">
            </button>
            <div class="nav-logo">
                <img src="./graphics/logo/greekgodslogo.png" alt="GreekGods" title="GreekGods" onclick="location.reload(); return false;">
            </div>
            <button class="nav-menu-profile" id="nav-menu-profile" onclick="window.location.href='./files/register.html'">
                <img src="./graphics/svg/profile.svg" alt="Profile" title="Profile">
            </button>
            <ul class="nav-links" id="nav-links">
                <li><a href="index.php" onclick="location.reload(); return false;">HOME</a></li>
                <li><a href="./files/program.php">PROGRAM</a></li>
                <li><a href="./files/blog.php">BLOG</a></li>
                <li><a href="./files/calculator.php">CALCULATOR</a></li>
                <li><a href="./files/about.php">ABOUT</a></li>
            </ul>
            <div class="nav-button">
                <button id="register-button" onclick="window.location.href='./files/register.html'">GET STARTED</button>
                <button id="profile-button" onclick="window.location.href='./files/profile.php'"><img src="./graphics/svg/profile.svg" alt="Profile" title="Profile"></button>
                <span id="profile-name"><?php echo htmlspecialchars($firstName . " " . $lastName); ?></span>
            </div>
        </nav>
        <header>
            <div class="header-container">
                <img src="./graphics/images/welcome-image.png" alt="people barbell exercise">
                <div class="header-add">
                    <span class="header-descriptions">
                        <p>Build confidence, be stronger, and transform.</p>
                        <p>START YOUR JOURNEY NOW.</p>
                    </span>
                    <button onclick="window.location.href='./files/register.html'">REGISTER</button>
                </div>
            </div>
        </header>
        <main class="index-main">
            <div class="index-section-container">
                <div class="section-workouts" id="picture">
                    <img src="./graphics/images/home.jpg" alt="Progress Tracking">
                </div>
                <div class="section-workouts" id="description">
                    <h3>Calculate your body statistics</h3>
                    <p>Stay on top of your fitness with real-time updates on your BMI, BMR, and workout achievements.</p>
                    <button onclick="window.location.href='./files/calculator.php'">Calculate Now</button>
                </div>
            </div>
            <div class="index-section-container" id="reverse">
                <div class="section-workouts" id="description">
                    <h3>Customized Workouts for Every Goal</h3>
                    <p>Whether you're looking to lose weight, gain muscle, or maintain health, GreekGods got you covered.</p>
                    <button onclick="window.location.href='./files/program.php'">Customize</button>
                </div>
                <div class="section-workouts" id="picture">
                    <img src="./graphics/images/home1.jpg" alt="Customizing Plan">
                </div>
            </div>
            <div class="index-section-container">
                <div class="section-workouts" id="picture">
                    <img src="./graphics/images/home2.jpg" alt="Fitness Tips">
                </div>
                <div class="section-workouts" id="description">
                    <h3>Daily Fitness Tips</h3>
                    <p>Get actionable tips and tricks to keep you motivated and on track.</p>
                    <button onclick="window.location.href='./files/blog.php'">Learn Now</button>
                </div>
            </div>
            <div class="index-section-container" id="reverse">
                <div class="section-workouts" id="description">
                    <h3>Completely Free</h3>
                    <p>All our tools are available to you at no cost, ever!</p>
                    <button onclick="window.location.href='./files/about.php'">Know more about GreekGods</button>
                </div>
                <div class="section-workouts" id="picture">
                    <img src="./graphics/images/home3.jpg" alt="Learn More">
                </div>
            </div>
        </main>
        <footer>
            <div class="footer-container">
                <ul class="footer-links">
                    <li><a href="index.html" onclick="location.reload(); return false;">HOME</a></li>
                    <li><a href="./files/blog.php">BLOG</a></li>
                    <li><a href="./files/about.php">ABOUT</a></li>
                    <li><a href="./files/laws.html">DISCLAIMER</a></li>
                    <li><a href="./files/about.php">CONTACT</a></li>
                    <li><a href="./files/laws.html">PRIVACY POLICY</a></li>
                    <li><a href="./files/laws.html">TERMS OF USE</a></li>
                </ul>
                <div class="footer-socials">
                    <a href="https://www.facebook.com" target="_blank">
                        <img src="./graphics/socials/facebook.png" alt="Facebook" title="Facebook">
                    </a>
                    <a href="https://www.instagram.com" target="_blank">
                        <img src="./graphics/socials/instagram.png" alt="Instagram" title="Instagram">
                    </a>
                    <a href="https://www.twitter.com" target="_blank">
                        <img src="./graphics/socials/twitter.png" alt="Twitter" title="Twitter">
                    </a>
                </div>
                <div class="footer-copyright">
                    <p>&copy; 2024 GreekGods. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    <script src="./index.js"></script>
</body>
</html>