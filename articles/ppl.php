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
    <title>GreekGods | Push Pull Legs Split (PPL)</title>
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
            <h2>Push, Pull, and Legs – The Essential Workout Split</h2>
            <p>
                The Push, Pull, and Legs (PPL) workout split is one of the most effective and popular training methods for building strength and muscle mass. This split divides your training into three primary categories based on the muscle groups you’re targeting: push exercises, pull exercises, and leg exercises.
            </p>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="main-info">
                <h3>1. Push Workouts</h3>
                <p>Push exercises focus on the muscles involved in pushing movements. These include the chest, shoulders, and triceps. The primary movements include pressing motions such as bench presses, overhead presses, and triceps extensions. These exercises work on building upper body strength and muscle endurance.</p>
                <ul>
                    <li><strong>Bench Press:</strong> Flat, Incline, Decline</li>
                    <li><strong>Overhead Shoulder Press:</strong> Barbell, Dumbbell</li>
                    <li><strong>Push-ups</strong></li>
                    <li><strong>Triceps Dips</strong></li>
                    <li><strong>Triceps Kickbacks</strong></li>
                    <li><strong>Lateral Raises</strong></li>
                </ul>
                <p><strong>Benefits of Push Workouts:</strong></p>
                <ul>
                    <li>Build upper body pushing strength</li>
                    <li>Improve overall chest, shoulder, and tricep development</li>
                    <li>Great for functional strength in daily activities</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>2. Pull Workouts</h3>
                <p>Pull exercises target the muscles involved in pulling movements. These primarily include the back muscles and biceps. The main movements here are pulling motions such as rows and pull-ups. Pull exercises are essential for balancing out push exercises and improving posture.</p>
                <ul>
                    <li><strong>Pull-Ups or Chin-Ups</strong></li>
                    <li><strong>Barbell Rows or Dumbbell Rows</strong></li>
                    <li><strong>Lat Pulldowns</strong></li>
                    <li><strong>Bicep Curls</strong></li>
                    <li><strong>Face Pulls</strong></li>
                    <li><strong>Deadlifts</strong></li>
                </ul>
                <p><strong>Benefits of Pull Workouts:</strong></p>
                <ul>
                    <li>Strengthens back and biceps</li>
                    <li>Improves posture by strengthening the upper back</li>
                    <li>Balances out pushing movements, reducing the risk of muscle imbalances</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>3. Leg Workouts</h3>
                <p>Leg exercises work the lower body, including the quadriceps, hamstrings, glutes, calves, and lower back. These exercises focus on some of the largest muscle groups in the body, which are responsible for overall strength and power.</p>
                <ul>
                    <li><strong>Squats:</strong> Back Squat, Front Squat, Goblet Squat</li>
                    <li><strong>Lunges:</strong> Walking Lunges, Stationary Lunges</li>
                    <li><strong>Deadlifts</strong></li>
                    <li><strong>Leg Press</strong></li>
                    <li><strong>Leg Extensions</strong></li>
                    <li><strong>Calf Raises</strong></li>
                </ul>
                <p><strong>Benefits of Leg Workouts:</strong></p>
                <ul>
                    <li>Builds overall strength and muscle mass</li>
                    <li>Improves athletic performance and endurance</li>
                    <li>Enhances lower body stability and mobility</li>
                </ul>
            </div>
            <div class="main-info">
                <h3>Why the PPL Split Works</h3>
                <p>The Push, Pull, and Legs split is effective because it allows for optimal recovery. By alternating between muscle groups, you give muscles time to rest while you target other parts of the body. This provides a well-rounded and sustainable approach to training.</p>
                <p><strong>How to Structure a PPL Workout:</strong></p>
                <ul>
                    <li><strong>3-Day Split:</strong> Push, Pull, Legs (with rest days in between)</li>
                    <li><strong>6-Day Split:</strong> Push, Pull, Legs (repeated twice a week)</li>
                </ul>
                <p>This split is versatile, meaning it can be adjusted based on your fitness level and goals. Whether you're training for strength, hypertrophy, or endurance, the PPL split is adaptable to suit any athlete's needs.</p>
            </div>
        </div>
    </main>
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
                <p><strong>Mifflin-St Jeor Equation</strong></p>
                <p><strong>For Men:</strong></p>
                <p>BMR = 10 × weight (kg) + 6.25 × height (cm) - 5 × age (years) + 5</p>
                <p><strong>For Women:</strong></p>
                <p>BMR = 10 × weight (kg) + 6.25 × height (cm) - 5 × age (years) - 161</p>            
                <p>Example Calculation</p>
                <p>Consider a 30-year-old woman weighing 60 kg and measuring 165 cm tall. Her BMR is calculated as follows:</p>
                <p>BMR = (10 × 60) + (6.25 × 165) - (5 × 30) - 161 = 1320.25 calories/day</p>
            </div>
        
            <div class="main-info">
                <h3>Factors That Influence BMR</h3>
                <ul>
                    <li><strong>Age:</strong> BMR decreases with age due to a decline in lean muscle mass and changes in hormonal activity.</li>
                    <li><strong>Gender:</strong> Men generally exhibit a higher BMR than women, primarily because they tend to have more lean muscle tissue, which burns more calories than fat.</li>
                    <li><strong>Body Composition:</strong> Individuals with a higher proportion of muscle mass experience an elevated BMR compared to those with greater fat mass.</li>
                    <li><strong>Genetics:</strong> Genetic predispositions can influence metabolic efficiency, impacting BMR independently of lifestyle factors.</li>
                    <li><strong>Health Conditions:</strong> Diseases, thyroid dysfunction, and hormonal imbalances can either elevate or suppress BMR.</li>
                </ul>
            </div>
        
            <div class="main-info">
                <h3>BMR and Total Daily Energy Expenditure (TDEE)</h3>
                <p>While BMR reflects the calories needed for vital functions, Total Daily Energy Expenditure (TDEE) includes additional energy spent on activities and food metabolism. TDEE is calculated by multiplying BMR with an activity factor, which accounts for lifestyle and exercise habits, ranging from sedentary to very active.</p>
            </div>
        
            <div class="main-info">
                <h3>Strategies to Improve BMR</h3>
                <ul>
                    <li><strong>Increase Muscle Mass:</strong> Strength training builds muscle, which burns more calories at rest than fat.</li>
                    <li><strong>Stay Active:</strong> Engaging in regular exercise, including aerobic and anaerobic activities, enhances metabolic rate over time.</li>
                    <li><strong>Consume Adequate Protein:</strong> A high-protein diet supports muscle maintenance and increases the thermic effect of food (TEF), leading to higher calorie expenditure during digestion.</li>
                    <li><strong>Avoid Extreme Dieting:</strong> Severely restricting calories can trigger metabolic adaptations, reducing BMR as the body conserves energy.</li>
                </ul>
            </div>
        
            <div class="main-info">
                <h3>Conclusion</h3>
                <p>Basal Metabolic Rate (BMR) is a fundamental measure of how the body utilizes energy to maintain life-sustaining processes. By understanding BMR, individuals can tailor their caloric intake and activity levels to achieve specific health goals. Whether for weight loss, maintenance, or muscle gain, incorporating BMR into one’s fitness strategy provides a scientific foundation for success.</p>
            </div>
        
            <div class="main-info">
                <h3>References</h3>
                <ol>
                    <li>R. J. Maughan, Nutrition and Sport. CRC Press, 2013.</li>
                    <li>J. R. Speakman, "Basal metabolic rate and body composition," International Journal of Obesity, vol. 18, no. 6, pp. 397–403, 1994.</li>
                    <li>A. Tremblay and J.-P. Chaput, "Adaptive reduction in thermogenesis and resistance to lose fat in obese men," The British Journal of Nutrition, vol. 104, no. 4, pp. 525–532, 2010.</li>
                    <li>H. Westerterp, "Energy expenditure and aging," Nutrition Reviews, vol. 71, no. 5, pp. 439–445, 2013.</li>
                </ol>
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
