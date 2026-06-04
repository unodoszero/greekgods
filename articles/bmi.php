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
    <title>GreekGods | Body Mass Index (BMI)</title>
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
            <h2>Understanding Body Mass Index (BMI)</h2>
            <p>Body Mass Index (BMI) is a widely used tool for assessing an individual's body weight in relation to their height. It provides a simple and quick way to categorize people as underweight, normal weight, overweight, or obese. BMI is frequently used in clinical and public health settings as an indicator of health risks associated with body weight.</p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>What Is BMI?</h3>
                <p>BMI is a numerical value calculated using a person’s weight and height. The formula is:</p>
                <p><strong>BMI = weight (kg) / height (m)²</strong></p>
                <p>For those using pounds and inches:</p>
                <p><strong>BMI = (weight (lbs) * 703) / (height (in)²)</strong></p>
                <p>The result is expressed in units of kg/m² and falls into one of the following categories, according to the World Health Organization (WHO):</p>
                <ul>
                    <li>Underweight: BMI < 18.5</li>
                    <li>Normal weight: BMI 18.5–24.9</li>
                    <li>Overweight: BMI 25.0–29.9</li>
                    <li>Obesity: BMI ≥ 30.0</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Strengths of BMI</h3>
                <ul>
                    <li><strong>Ease of Use:</strong> BMI is simple to calculate and doesn’t require special equipment.</li>
                    <li><strong>Standardization:</strong> It provides a universal method for classifying weight status.</li>
                    <li><strong>Population Health:</strong> BMI is a useful metric for tracking health trends and obesity prevalence in populations.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Limitations of BMI</h3>
                <ul>
                    <li><strong>Does Not Distinguish Between Fat and Muscle:</strong> Muscular individuals may have a high BMI despite low body fat levels.</li>
                    <li><strong>Ignores Fat Distribution:</strong> Visceral fat, which poses greater health risks, is not differentiated from subcutaneous fat.</li>
                    <li><strong>Not Individualized:</strong> Factors like age, gender, ethnicity, and fitness levels are not accounted for.</li>
                    <li><strong>Potential Stigma:</strong> BMI classifications can lead to weight-related stigma or oversimplification of complex health conditions.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Conclusion</h3>
                <p>Body Mass Index (BMI) is a widely used, albeit imperfect, tool for evaluating weight-related health risks. It is best used as a screening measure rather than a diagnostic one. For a more precise understanding of health and fitness, BMI should be interpreted alongside other metrics and in consultation with healthcare professionals.</p>
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
