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
    <title>GreekGods | Protein</title>
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
            <h2>Fueling Your Fitness: Building a Balanced Diet for Fitness Success</h2>
            <p>
                Proteins are essential building blocks for our bodies, playing a key role in muscle repair and recovery after a workout. Whether you’re hitting the gym to build muscle, improve endurance, or lose weight, understanding the relationship between protein and exercise is vital for maximizing your fitness goals. However, as with any nutritional element, balance is key, and not all protein-focused diets are ideal in the long term.
            </p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>The Role of Protein in Exercise</h3>
                <ul>
                    <li><strong>Muscle Repair and Growth:</strong> During intense workouts, muscle fibers are broken down. Protein helps repair these fibers, making them stronger.</li>
                    <li><strong>Energy Support:</strong> While carbs are the body’s preferred fuel source, proteins can serve as a backup energy source during long or intense exercise sessions.</li>
                    <li><strong>Satiety and Weight Management:</strong> Protein helps you feel full longer, which can reduce overall calorie intake.</li>
                </ul>
                <p>The general guideline for active individuals is to consume between 1.2 to 2.0 grams of protein per kilogram of body weight daily. For example, a 70-kg person may need anywhere from 84 to 140 grams of protein, depending on their activity level and goals.</p>
            </div>
            <div class="main-info">
                <h3>Choosing the Right Proteins</h3>
                <p>Not all proteins are created equal. For lifelong weight-loss results and overall health, lean proteins are the way to go. Here are 10 excellent sources of lean protein to include in your diet:</p>
                <ul>
                    <li><strong>Fish:</strong> Rich in omega-3 fatty acids and low in calories, fish-like salmon and cod are great for muscle recovery.</li>
                    <li><strong>Seafood:</strong> Shrimp, scallops, and crab provide high-quality protein with minimal fat.</li>
                    <li><strong>Skinless, White-Meat Poultry:</strong> Chicken and turkey are versatile, lean protein options.</li>
                    <li><strong>Lean Beef:</strong> Cuts like tenderloin, sirloin, and eye of round offer iron and protein with reduced fat.</li>
                    <li><strong>Skim or Low-Fat Milk:</strong> A great source of calcium and protein for post-workout recovery.</li>
                    <li><strong>Skim or Low-Fat Yogurt:</strong> Packed with probiotics and protein, it’s ideal for gut health and muscle repair.</li>
                    <li><strong>Fat-Free or Low-Fat Cheese:</strong> Adds protein to meals or snacks without excess calories.</li>
                    <li><strong>Eggs:</strong> Affordable, easy to prepare, and packed with high-quality protein.</li>
                    <li><strong>Lean Pork (Tenderloin):</strong> Offers protein and essential nutrients like thiamine.</li>
                    <li><strong>Beans and Lentils:</strong> A plant-based protein powerhouse, rich in fiber and iron.</li>
                    <li><strong>Nuts and Seeds:</strong> While slightly higher in fat, they’re an excellent source of healthy fats and protein for energy.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Protein-Centric Diets: A Short-Term Solution?</h3>
                <p>Some popular diets, such as the Atkins Diet and the Ketogenic Diet, emphasize high protein and fat intake while restricting carbohydrates. These diets often result in quick weight loss, primarily due to reduced calorie intake and water weight loss from carb restriction.</p>
                <p>However, research shows these diets tend to be more effective in the short term rather than for sustained health and fitness. Why?</p>
                <ul>
                    <li><strong>Adherence Challenges:</strong> Many people find it difficult to maintain such restrictive eating patterns over time.</li>
                    <li><strong>Nutritional Imbalance:</strong> Focusing heavily on protein and fat can mean missing out on essential nutrients, leading to deficiencies and potential health risks.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Potential Downsides of Protein-Heavy Diets</h3>
                <p>While protein is a superstar nutrient, excessive reliance on high-protein diets, particularly at the expense of other food groups, can lead to unhealthy side effects, including:</p>
                <ul>
                    <li>Fatigue and Dizziness: A lack of carbs can deprive your body of its primary energy source, leading to sluggishness.</li>
                    <li>Headaches: Ketosis, a state induced by low-carb diets, can trigger headaches for some individuals.</li>
                    <li>Bad Breath: Byproducts of fat metabolism in ketogenic diets can cause halitosis.</li>
                    <li>Constipation: Limited fiber intake from low-carb diets can disrupt digestion.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Striking the Right Nutritional Balance</h3>
                <p>Instead of focusing solely on protein, aim for a balanced diet that includes:</p>
                <ul>
                    <li><strong>Complex Carbohydrates:</strong> Foods like whole grains, fruits, and vegetables provide energy and essential nutrients.</li>
                    <li><strong>Healthy Fats:</strong> Include sources like avocados, nuts, seeds, and fatty fish for sustained energy and overall health.</li>
                    <li><strong>Adequate Protein:</strong> Lean meats, dairy, eggs, legumes, and plant-based alternatives are excellent protein sources.</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Key Takeaways for Your Fitness Journey</h3>
                <ul>
                    <li>Prioritize balance: A diet too heavy in any one macronutrient can lead to imbalances and health issues.</li>
                    <li>Customize your intake: Your protein needs depend on your workout intensity, goals, and overall health.</li>
                    <li>Listen to your body: Pay attention to signs of fatigue, digestive issues, or other negative effects, and adjust your diet accordingly.</li>
                </ul>
                <p>Proteins are vital, but they’re just one piece of the fitness puzzle. By adopting a well-rounded eating plan, you’ll not only support your workout performance but also maintain long-term health and vitality.</p>
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
