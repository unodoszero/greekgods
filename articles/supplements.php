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
    <title>GreekGods | Supplements</title>
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
            <h2>Understanding Fitness Supplements: What You Need to Know</h2>
            <p>Learn about the most popular fitness supplements, their benefits, and how to choose the right ones for your goals.</p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>What Are Fitness Supplements?</h3>
                <p>Fitness supplements are products designed to complement your diet and exercise regimen. They come in various forms, including powders, capsules, tablets, and liquids, and they typically contain one or more active ingredients that support specific aspects of fitness.</p>
                <p>Common types of fitness supplements include:</p>
                <ul>
                    <li>Protein Supplements</li>
                    <li>Pre-Workout Supplements</li>
                    <li>Post-Workout Supplements</li>
                    <li>Fat Burners</li>
                    <li>Vitamins and Minerals</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>Protein Supplements</h3>
                <p>Protein supplements are among the most popular fitness supplements. They come in various forms, including whey, casein, and plant-based proteins.</p>
                <h3>Benefits:</h3>
                <ul>
                    <li>Supports muscle growth and repair</li>
                    <li>Aids in post-workout recovery</li>
                    <li>Convenient and easy to use</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>Pre-Workout Supplements</h3>
                <p>Pre-workout supplements are designed to be consumed before exercise to boost energy, endurance, and focus. They contain ingredients like caffeine, creatine, and beta-alanine.</p>
                <h3>Benefits:</h3>
                <ul>
                    <li>Increases energy levels</li>
                    <li>Enhances focus and mental clarity</li>
                    <li>Improves endurance and workout performance</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>Post-Workout Supplements</h3>
                <p>Post-workout supplements help your body recover by replenishing glycogen stores and aiding in muscle repair.</p>
                <h3>Benefits:</h3>
                <ul>
                    <li>Speeds up recovery time</li>
                    <li>Replenishes glycogen stores</li>
                    <li>Reduces muscle soreness</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>Fat Burners</h3>
                <p>Fat burners support fat loss by increasing metabolism, suppressing appetite, or improving fat oxidation.</p>
                <h3>Benefits:</h3>
                <ul>
                    <li>Increases metabolic rate</li>
                    <li>Suppresses appetite</li>
                    <li>Boosts energy levels</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>Vitamins and Minerals</h3>
                <p>Vitamins and minerals are essential nutrients needed for overall health. Some individuals may benefit from supplementation to fill any dietary gaps.</p>
                <h3>Benefits:</h3>
                <ul>
                    <li>Supports immune system health</li>
                    <li>Helps in muscle function and recovery</li>
                    <li>Ensures optimal overall health</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>How to Choose the Right Supplements</h3>
                <ul>
                    <li>Consult a healthcare professional</li>
                    <li>Do your research</li>
                    <li>Prioritize whole foods</li>
                    <li>Start slow</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>Takeaways</h3>
                <p>Fitness supplements can complement your workout routine, helping you meet your goals. However, focus on whole foods first and choose the right supplements for your specific needs.</p>
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
