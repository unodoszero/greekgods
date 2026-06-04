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
    <title>GreekGods | Progressive Overload</title>
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
            <h2>Unlocking Gains with Progressive Overload</h2>
            <p>Progressive overload helps you build strength and muscle by gradually increasing the intensity of your workouts, such as adding weight, reps, or sets.</p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>What Is Progressive Overload Training?</h3>
                <p>Progressive overload refers to the practice of gradually increasing the weight, reps, or sets of an exercise to challenge the muscles. This ensures that your body adapts and becomes stronger over time, leading to increased muscle mass and strength.</p>
            </div>
            <div class="main-info">
                <h3>Benefits of Progressive Overload</h3>
                <ul>
                    <li><strong>Muscle Growth:</strong> Progressive overload results in increased muscle size over time.</li>
                    <li><strong>Increased Strength:</strong> Allows you to lift heavier weights and improve strength.</li>
                    <li><strong>Improved Muscle Endurance:</strong> Challenges your muscles to work longer, improving endurance.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Risks of Progressive Overload</h3>
                <ul>
                    <li><strong>Injuries:</strong> Increasing weight or intensity too quickly can lead to injuries.</li>
                    <li><strong>Overtraining:</strong> Pushing yourself too hard without recovery can lead to fatigue and performance decline.</li>
                    <li><strong>Plateauing:</strong> Increasing intensity too frequently can lead to a training plateau, causing muscle growth to stall.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>How to Avoid the Risks</h3>
                <ul>
                    <li><strong>Follow the 10% Rule:</strong> Gradually increase your weight, reps, or sets by no more than 10% at a time.</li>
                    <li><strong>Allow Adequate Rest:</strong> Rest for at least 24-72 hours between workouts depending on the intensity.</li>
                    <li><strong>Vary Your Workouts:</strong> Cross-train and rotate exercises to avoid overtraining.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>How to Do Progressive Overload</h3>
                <p><strong>1. Increase Weight:</strong> Gradually add weight to your lifts as your body adapts.</p>
                <ul>
                    <li>Once you can perform the upper end of your rep range with good form, increase the weight by 2.5-5 lbs.</li>
                    <li>Small increments prevent injury and help maintain form.</li>
                </ul>
                <p><strong>2. Increase Endurance:</strong> Increase reps to add intensity if you're not yet ready for more weight.</p>
                <ul>
                    <li>Start with a rep range of 6-8 reps and gradually increase to 10-12 reps.</li>
                </ul>
                <p><strong>3. Increase Sets:</strong> Add more sets to increase the total volume of your workout.</p>
                <ul>
                    <li>Start with 3 sets and gradually increase to 4 or 5 sets per exercise.</li>
                </ul>
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
