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
    <title>GreekGods | Calorie Deficit</title>
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
            <h2>Calorie Deficit 101: Your Key to Effective Weight Loss</h2>
            <p>
                A calorie deficit is a cornerstone of weight loss. By consuming fewer calories than your body burns, you can tap into stored energy reserves, leading to sustainable and effective fat loss.
            </p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>What is a Calorie Deficit?</h3>
                <p>
                    A calorie deficit occurs when you burn more calories than you consume. This energy imbalance forces your body to use stored fat for fuel, making it the foundation of weight loss.
                </p>
            </div>
            <div class="main-info">
                <h3>Understanding Calories and Energy Storage</h3>
                <p><strong>What Are Calories?</strong></p>
                <p>
                    Calories are units of energy found in food and drinks, essential for powering every bodily function, from breathing to moving.
                </p>
                <p><strong>Energy Storage in the Body:</strong></p>
                <ul>
                    <li><strong>Fat:</strong> Long-term energy storage.</li>
                    <li><strong>Carbohydrates:</strong> Stored as glycogen for short-term energy needs.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>What Happens When You Consume More or Fewer Calories?</h3>
                <ul>
                    <li><strong>Excess Calories:</strong> Stored as fat, leading to weight gain.</li>
                    <li><strong>Calorie Deficit:</strong> Your body burns stored fat to meet energy demands, resulting in weight loss.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Activities to Create a Calorie Deficit</h3>
                <p><strong>To Do:</strong></p>
                <ul>
                    <li><strong>Cardiovascular Exercise:</strong> Engage in running, cycling, or brisk walking for 150–300 minutes weekly.</li>
                    <li><strong>Strength Training:</strong> Build muscle to increase resting metabolic rate. Aim for 2–3 sessions per week.</li>
                    <li><strong>Daily Movement:</strong> Stay active through walking, taking the stairs, or using a standing desk.</li>
                    <li><strong>HIIT:</strong> Burn calories efficiently with short bursts of intense activity followed by rest.</li>
                    <li><strong>Fun Activities:</strong> Dancing, gardening, or playing sports are enjoyable calorie-burning options.</li>
                </ul>
                <p><strong>To Avoid:</strong></p>
                <ul>
                    <li>Prolonged sitting or sedentary behavior.</li>
                    <li>Overtraining without proper recovery.</li>
                    <li>Relying solely on exercise without addressing diet.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Why Is a Calorie Deficit Important for Weight Loss?</h3>
                <p>
                    Weight loss is impossible without a calorie deficit. It creates the energy imbalance needed to force your body to break down fat stores for energy. However, aim for a sustainable deficit to ensure steady progress without compromising your health.
                </p>
            </div>
            <div class="main-info">
                <h3>Conclusion</h3>
                <p>
                    A calorie deficit is a powerful tool for achieving weight loss. By balancing exercise, diet, and lifestyle changes, you can create a sustainable approach to reach your fitness goals safely and effectively.
                </p>
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
