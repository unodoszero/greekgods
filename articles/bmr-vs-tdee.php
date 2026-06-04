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
    <title>GreekGods | BMR vs TDEE</title>
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
        <button class="nav-menu-profile" id="nav-menu-profile" onclick="window.location.href='../files/register.php'">
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
            <button id="register-button" onclick="window.location.href='../files/register.php'">GET STARTED</button>
            <button id="profile-button" onclick="window.location.href='../files/profile.php'">
                <img src="../graphics/svg/profile.svg" alt="Profile" title="Profile">
            </button>
            <span id="profile-name"><?php echo htmlspecialchars($firstName . " " . $lastName); ?></span>
        </div>
    </nav>
    <header>
        <div class="header-container">
            <h2>What is Basal Metabolic Rate (BMR)?</h2>
            <p>Basal Metabolic Rate (BMR) refers to the minimum amount of energy, measured in calories, that the human body requires to maintain vital physiological functions at rest, such as breathing, circulation, and cellular processes. This energy expenditure is critical to sustaining life, even when an individual is not engaged in physical activity.</p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>Why is BMR Important?</h3>
                <p>BMR plays a foundational role in understanding an individual's overall energy needs. It forms the largest component of Total Daily Energy Expenditure (TDEE), which combines BMR with energy burned through physical activity and food digestion. Recognizing one’s BMR allows for precise adjustments in caloric intake, which is crucial for achieving health and fitness goals, such as weight management, muscle gain, or general well-being.</p>
                <ul>
                    <li><strong>For Weight Loss:</strong> A caloric deficit—consuming fewer calories than one’s TDEE—can result in weight loss. Knowing BMR ensures this deficit is calculated correctly without compromising basic bodily functions.</li>
                    <li><strong>For Weight Gain:</strong> Conversely, a caloric surplus—eating more than TDEE—can aid in muscle growth or weight gain, particularly when paired with resistance training.</li>
                    <li><strong>For Maintenance:</strong> To maintain weight, caloric intake should align with TDEE, factoring in BMR and other energy expenditures.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>How is BMR Calculated?</h3>
                <p>The calculation of BMR depends on individual factors such as age, gender, weight, and height, as well as lean body mass. Two widely recognized methods for estimating BMR are the Harris-Benedict Equation and the Mifflin-St Jeor Equation, with the latter being preferred for its improved accuracy in modern populations.</p>
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