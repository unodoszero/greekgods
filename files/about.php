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
    <link rel="stylesheet" href="./about.css">
    <script type="text/javascript">
        const userId = <?php echo json_encode($userId); ?>;
    </script>
    <title>GreekGods | About</title>
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
            <li><a href="./blog.php">BLOG</a></li>
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
        <h1>About GreekGods</h1>
        <img src="../graphics/images/about-image.png" alt="Workouts">
    </header>
    <main>
        <section>
            <h2>Our Mission</h2>
            <p>At GreekGods, our mission is to empower individuals to achieve their ultimate fitness goals through personalized tools, expert advice, and a supportive community. We believe in the power of combining ancient wisdom with modern science to create a holistic approach to fitness. Whether you're just starting out or are a seasoned athlete, we are here to guide you every step of the way with tailored workout plans, nutrition articles, and advanced calculators to track your progress. Join us and transform your fitness journey with the strength of the ancients and the knowledge of today.</p>
        </section>
        <section>
            <h2>Our Story</h2>
            <p>GreekGods was born from a passion for the legendary physiques of ancient warriors and gods. We sought to blend the timeless aesthetics of the ancients with the latest advancements in fitness science. Our journey began with a simple idea: to create a platform that offers comprehensive fitness solutions for everyone. Today, GreekGods stands as a testament to the power of dedication, innovation, and the pursuit of excellence.</p>
        </section>
        <section>
            <h2>What We Offer</h2>
            <ul>
                <li>Comprehensive calculators for BMI, BMR, and TDEE to help you understand your body's needs.</li>
                <li>Science-backed fitness and nutrition articles to keep you informed and motivated.</li>
                <li>Tailored workout plans designed for all fitness levels, from beginners to advanced athletes.</li>
            </ul>
        </section>
        <section>
            <h2>Why Choose Us?</h2>
            <p>Unlike other platforms, GreekGods focuses on a holistic approach to fitness. We believe that true fitness is achieved through a balance of nutrition, exercise, and mindset. Our platform offers a unique blend of ancient wisdom and modern science to help you achieve lasting results. With GreekGods, you're not just working out; you're embarking on a transformative journey towards a healthier, stronger, and more confident you.</p>
        </section>
        <section>
            <h2>Testimonials</h2>
            <p>"GreekGods has completely transformed my fitness journey. The personalized workout plans and expert advice have helped me achieve goals I never thought possible. I feel stronger, healthier, and more confident than ever before!" - Alex M.</p>
            <p>"The community at GreekGods is incredibly supportive and motivating. I've learned so much about fitness and nutrition, and I've made great progress thanks to their comprehensive tools and resources." - Jamie L.</p>
        </section>
        <section>
            <h2>Join Us Today</h2>
            <p>Discover your true fitness potential with GreekGods. Explore our tools, read our guides, and start transforming your life today. Whether you're looking to build muscle, lose weight, or simply improve your overall health, we have everything you need to succeed. Join our community and embark on a journey towards a stronger, healthier, and more empowered you.</p>
        </section>    
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
