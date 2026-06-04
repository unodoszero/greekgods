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
    <title>GreekGods | Isolation vs. Composite</title>
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
            <h2>Isolation or Composite? How to Choose the Right Exercises for Your Goals</h2>
            <p>Explore the key differences between isolation and composite exercises and learn how each can benefit your fitness goals.</p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>What Are Isolation Exercises?</h3>
                <p><strong>Isolation exercises</strong> are movements that target a specific muscle group or joint. These exercises isolate the muscle being worked and typically involve only one joint in the movement.</p>
                <p>Common examples of isolation exercises include:</p>
                <ul>
                    <li>Bicep curls (targeting the biceps)</li>
                    <li>Leg extensions (targeting the quadriceps)</li>
                    <li>Triceps pushdowns (targeting the triceps)</li>
                    <li>Lateral raises (targeting the deltoids)</li>
                    <li>Leg curls (targeting the hamstrings)</li>
                </ul>
                <h3>Benefits of Isolation Exercises:</h3>
                <ul>
                    <li>Muscle Focus and Definition</li>
                    <li>Rehabilitation and Injury Prevention</li>
                    <li>Targeting Weak Points</li>
                    <li>Muscle Activation</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>What Are Compound (Composite) Exercises?</h3>
                <p><strong>Compound exercises</strong> involve multiple joints and muscle groups in a single movement, allowing for more comprehensive strength-building.</p>
                <p>Common examples include:</p>
                <ul>
                    <li>Squats (targeting the quadriceps, hamstrings, glutes, and core)</li>
                    <li>Deadlifts (targeting the hamstrings, glutes, lower back, and core)</li>
                    <li>Bench press (targeting the chest, shoulders, and triceps)</li>
                    <li>Pull-ups/Chin-ups (targeting the back, biceps, and shoulders)</li>
                    <li>Overhead press (targeting the shoulders, triceps, and upper chest)</li>
                </ul>
                <h3>Benefits of Compound Exercises:</h3>
                <ul>
                    <li>Efficiency</li>
                    <li>Strength Building</li>
                    <li>Calorie Burning and Fat Loss</li>
                    <li>Functional Fitness</li>
                    <li>Hormonal Benefits</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>When to Use Isolation Exercises:</h3>
                <ul>
                    <li>For Muscle Definition</li>
                    <li>To Address Muscle Imbalances</li>
                    <li>During Rehab</li>
                </ul>

                <h3>When to Use Compound Exercises:</h3>
                <ul>
                    <li>For Overall Strength and Muscle Mass</li>
                    <li>To Improve Functional Fitness</li>
                    <li>When Short on Time</li>
                </ul>
            </div>

            <div class="main-info">
                <h3>Combining Both: A Balanced Approach</h3>
                <ul>
                    <li>Warm-up with Compound Movements</li>
                    <li>Finish with Isolation</li>
                    <li>Periodization</li>
                </ul>
            </div>
            
            <div class="main-info">
                <h3>Takeaways</h3>
                <p>Both isolation and compound exercises have their place in a well-rounded fitness program. Combining them effectively can optimize your workout for strength, aesthetics, or performance.</p>
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
