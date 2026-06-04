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
    <link rel="stylesheet" href="./calculator.css">
    <script type="text/javascript">
        const userId = <?php echo json_encode($userId); ?>;
    </script>
    <title>GreekGods | Calculator</title>
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
            <li><a href="./calculator.php" onclick="location.reload(); return false;">CALCULATOR</a></li>
            <li><a href="./about.php">ABOUT</a></li>
        </ul>
        <div class="nav-button">
            <button id="register-button" onclick="window.location.href='./register.html'">GET STARTED</button>
            <button id="profile-button" onclick="window.location.href='./profile.php'"><img src="../graphics/svg/profile.svg" alt="Profile" title="Profile"></button>
            <span id="profile-name"><?php echo htmlspecialchars($firstName . " " . $lastName); ?></span>
        </div>
    </nav>
    <header>
        <h2>Fitness Calculator</h2>
        <p>
            This calculator covers calculating your Body Mass Index (BMI), Basal Metabolic Rate (BMR), Total Daily Energy Exprenditure (TDEE), and Protein Intake. 
            Input your personal details such as age, gender, height, weight, and activity level to get accurate fitness statistics.
        </p>
        <div class="header-container">
            <form class="header-calculator" action="">
                <div class="header-first">
                    <div class="header-info" id="header-age">
                        <label for="age">Age</label>
                        <input id="age" type="number" name="age" min="12" max="100"/>
                    </div>
    
                    <div class="header-info" id="header-gender">
                        <span>Gender</span>
                        <input id="male" type="radio" value="male" name="gender"/>
                        <label for="male">Male</label>
                        <input id="female" type="radio" value="female" name="gender"/> 
                        <label for="female">Female</label>
                    </div>
                </div>

                <div class="header-second">
                    <div class="header-info" id="header-height">
                        <label for="height">Height</label>
                        <input id="height" type="text" name="height"/> 
                        <select name="heightMetrics" id="heightMetric">
                            <option value="cm">.cm</option>
                            <option value="in">.in</option>
                            <option value="m">.m</option>
                            <option value="ft">.ft</option>
                        </select>
                    </div>
    
                    <div class="header-info" id="header-weight">
                        <label for="weight">Weight</label>
                        <input id="weight" type="text" name="weight"/>
                        <select name="weightMetrics" id="weightMetric">
                            <option value="lb">.lb</option>
                            <option value="kg">.kg</option>
                        </select>
                    </div>
                </div>

                <div class="header-info" id="header-activity">
                    <label for="activity" data-target="#bmr-activity">Activity<span src="/graphics/svg/info-black.svg"></span></label>
                    <select name="activity" id="activity"> 
                        <option value="" disabled selected></option>
                        <option value="sedentary">Sedentary: little or no exercise</option>
                        <option value="light">Light: exercise 1-3 times/week</option>
                        <option value="moderate">Moderate: exercise 3-5 times/week</option>
                        <option value="active">Active: intense exercise 6-7 times/week</option>
                        <option value="very_active">Very Active: very intense exercise daily, or physical job</option>
                    </select> 
                </div>    
                
                <div class="header-info" id="header-formula">
                    <label for="formula" data-target="#section-bmr">Select Formula</label>
                    <select id="formula">
                        <option value="" disabled selected></option>
                        <option value="mifflin_st_jeor">Mifflin St. Jeor</option>
                        <option value="revised_harris_benedict">Revised Harris-Benedict</option>
                        <option value="katch_mcardle">Katch-McArdle</option>
                    </select>
                </div>                

                <div class="katch-mcardle-container" id="katchMcardleContainer" style="display: none;">
                    <label for="bodyFat">Body Fat:</label>
                    <input type="range" id="bodyFat" min="0" max="100" value="20" step="1">
                    <input type="number" id="sliderValue" min="0" max="100" value="20" step="1">
                </div>
              

                <input type="submit" value="Calculate" name="submit"/>
            </form>              
        </div>
    </header>
    <main>
        <div class="main-container">
            <div id="main-info" class="main-info">
                <p>Statistics</p>
                <p>Age: <span id="age-result">N/A</span></p>
                <p>Gender: <span id="gender-result">N/A</span></p>
                <p>Height: <span id="height-result">N/A</span></p>
                <p>Weight: <span id="weight-result">N/A</span></p>
                <p>Activity: <span id="activity-result">N/A</span></p>
            </div>
            <div class="main-statistics">
                <div class="main-bmi">
                    <table>
                        <th colspan="2">BODY MASS INDEX (BMI)</th>
                        <tr>
                            <td>BMI:</td>
                            <td><span id="bmi-result">N/A</span></td>
                        </tr>
                        <tr>
                            <td>Classification:</td>
                            <td><span id="classification-result">N/A</span></td>
                        </tr>
                    </table>
                </div>
                <div class="main-calorie">
                    <table>
                        <th colspan="2">TOTAL DAILY ENERGY EXPENDITURE (TDEE)</th>
                        <tr>
                            <td>Basal Metabolic Rate (BMR):</td>
                            <td><span id="bmr"></span></td>
                        </tr>
                        <tr>
                            <td>Maintain Weight:</td>
                            <td><span id="maintain-weight"></span></td>
                        </tr>
                        <tr>
                            <td>Mid Weight Loss:</td>
                            <td><span id="mid-weight-loss"></span></td>
                        </tr>
                        <tr>
                            <td>Weight Loss:</td>
                            <td><span id="weight-loss"></span></td>
                        </tr>
                        <tr>
                            <td>Extreme Weight Loss:</td>
                            <td><span id="extreme-weight-loss"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="main-protein">
                    <table>
                        <th colspan="2">PROTEIN</th>
                        <tr>
                            <td>Recommended Protein Intake:</td>
                            <td><span id="protein-intake"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <section>
        <div class="section-container">
            <hr>
            <div class="section-info" id="section-bmi">
                <h2>Body Mass Index (BMI)</h2>
                <p>
                    Body Mass Index (BMI) is an essential tool used in the fitness and healthcare industries to evaluate an individual's body weight in relation to their height. While it’s not a direct measure of body fat, BMI serves as an important screening method that helps categorize an individual’s weight status, providing insight into potential health risks. Whether you are underweight, have a healthy weight, are overweight, or obese, understanding your BMI can give you a clearer picture of how your body weight could be affecting your overall health.
                </p>
                <p>
                    Maintaining a healthy BMI is crucial because it is strongly correlated with physical and mental well-being, longevity, and the prevention of chronic diseases such as heart disease, type 2 diabetes, and hypertension. A BMI within the healthy range generally suggests that your body is at an optimal weight for maintaining peak performance, energy levels, and vitality. On the other hand, a BMI that falls outside of the healthy range may indicate the need for adjustments in diet, exercise, or lifestyle choices to reduce the risk of developing weight-related health conditions.
                </p>
                <p>
                    It’s important to note that BMI is just one piece of the puzzle. While it helps identify potential concerns, it does not take into account factors like muscle mass, bone density, or fat distribution, which can make BMI less accurate for athletes or those with a higher muscle-to-fat ratio. For example, a highly muscular individual may have a higher BMI but still have a very low body fat percentage.
                </p>
                <p>
                    The BMI formula is a simple way to determine where you fall on the BMI scale. Whether you use metric or imperial units, the calculation helps categorize your weight into one of four classifications. Let’s dive into how to calculate your BMI and understand what each category means for your health.
                </p>
                <p id="title-formula"><strong>Formula:</strong> To calculate BMI, use the following formulas:
                </p>
                <p id="title-formula"><strong>Standard Formula (Metric):</strong></p>
                <img id="bmi-formula-standard" src="../graphics/formulas/bmi-formula-standard.png" alt="BMI Formula - Metric" title="BMI Formula">
                <p id="title-formula"><strong>Imperial Formula:</strong></p>
                <img id="bmi-formula-imperial" src="../graphics/formulas/bmi-formula-imperial.png" alt="BMI Formula - Imperial" title="BMI Formula">
                <table>
                    <tr>
                        <td><strong>Classification</strong></td>
                        <td><strong>BMI Range</strong></td>
                    </tr>
                    <tr>
                        <td>Underweight</td>
                        <td>BMI < 18.5</td>
                    </tr>
                    <tr>
                        <td>Healthy Weight</td>
                        <td>18.5 - 24.9</td>
                    </tr>
                    <tr>
                        <td>Overweight</td>
                        <td>25 - 29.9</td>
                    </tr>
                    <tr>
                        <td>Obese</td>
                        <td>BMI ≥ 30</td>
                    </tr>
                </table>
                <p>
                    A BMI score within the “Healthy Weight” range (18.5 to 24.9) is generally considered optimal for most individuals. However, those in the "Overweight" or "Obese" ranges may benefit from focusing on improving their diet and physical activity levels to reduce their BMI, thereby lowering the risk of associated diseases. Conversely, individuals in the "Underweight" range should consult with a healthcare professional to ensure they are receiving adequate nutrition and support for their body’s needs.
                </p>
                <p>
                    It’s important to remember that BMI is only a starting point in understanding your body composition. For a more comprehensive assessment, consider consulting a fitness or healthcare professional who can evaluate other factors, such as body fat percentage, muscle mass, and overall fitness level.
                </p>
            </div>
            <hr>
            <div class="section-info" id="section-bmr">
                <h2>Basal Metabolic Rate (BMR)</h2>
                <p>
                    Basal Metabolic Rate (BMR) represents the number of calories your body requires to perform basic life-sustaining functions at rest. These functions include breathing, circulation, cell production, and maintaining body temperature. BMR accounts for the majority of your daily caloric expenditure and serves as the foundation for understanding your overall energy needs.
                </p>
                <p>
                    Understanding your BMR is crucial for managing your weight and designing a personalized fitness or nutrition plan. By knowing how many calories your body naturally burns at rest, you can adjust your dietary intake and physical activity levels to achieve specific goals, such as weight loss, muscle gain, or weight maintenance. Whether you're looking to build lean muscle or shed excess fat, knowing your BMR provides a baseline for making informed decisions.
                </p>
                <p>
                    BMR is influenced by various factors, including age, gender, weight, height, and body composition. For instance, individuals with higher muscle mass tend to have a higher BMR because muscle tissue burns more calories than fat tissue. This is why strength training is often recommended for those aiming to boost their metabolism and burn calories more efficiently.
                </p>
                <p>
                    The calculation of BMR involves well-established formulas, such as the Harris-Benedict Equation, which can be used with either metric or imperial units. These formulas provide an estimate of your caloric needs based on your specific characteristics. Let’s explore how to calculate your BMR using these methods.
                </p>
                <p id="title-formula"><strong>Formula:</strong> To calculate BMR, use the following formulas:
                </p>

                <p id="title-formula"><strong>Mifflin - St Jeor Equation:</strong></p>
                <p id="title-formula"><strong>(Metric) Male:</strong></p>
                <img id="bmr-formula" src="../graphics/formulas/bmr-mifflin-formula-male-metric.png" alt="BMR Formula - Metric Male" title="BMR Formula">
                <p id="title-formula"><strong>(Imperial) Male:</strong></p>
                <img id="bmr-formula" src="../graphics/formulas/bmr-mifflin-formula-male-imperial.png" alt="BMR Formula - Imperial Male" title="BMR Formula">
                <p id="title-formula"><strong>(Metric) Female:</strong></p>
                <img id="bmr-formula" src="../graphics/formulas/bmr-mifflin-formula-female-metric.png" alt="BMR Formula - Metric Female" title="BMR Formula">
                <p id="title-formula"><strong>(Imperial) Female:</strong></p>
                <img id="bmr-formula" src="../graphics/formulas/bmr-mifflin-formula-female-imperial.png" alt="BMR Formula - Imperial Female" title="BMR Formula">

                <p id="title-formula"><strong>Revised Harris-Benedict Equation:</strong></p>
                <p id="title-formula"><strong>(Metric) Male:</strong></p>
                <img id="bmr-formula" src="../graphics/formulas/bmr-harris-formula-male-metric.png" alt="BMR Formula - Metric Male" title="BMR Formula">
                <p id="title-formula"><strong>(Imperial) Male:</strong></p>
                <img id="bmr-formula" src="../graphics/formulas/bmr-harris-formula-male-imperial.png" alt="BMR Formula - Imperial Male" title="BMR Formula">
                <p id="title-formula"><strong>(Metric) Female:</strong></p>
                <img id="bmr-formula" src="../graphics/formulas/bmr-harris-formula-female-metric.png" alt="BMR Formula - Metric Female" title="BMR Formula">
                <p id="title-formula"><strong>(Imperial) Female:</strong></p>
                <img id="bmr-formula" src="../graphics/formulas/bmr-harris-formula-female-imperial.png" alt="BMR Formula - Imperial Female" title="BMR Formula">
                
                <p id="title-formula"><strong>Katch-McArdle Equation:</strong></p>
                <p id="title-formula"><strong>Katch-McArdle Lean Body Mass (LBM) Equation:</strong></p>
                <img id="bmr-katch-lbm-formula" src="../graphics/formulas/bmr-katch-lbm-formula.png" alt="BMR Formula - Imperial" title="BMR Formula">
                <p id="title-formula"><strong>Katch-McArdle Equation:</strong></p>
                <img id="bmr-katch-formula" src="../graphics/formulas/bmr-katch-formula.png" alt="BMR Formula - Imperial" title="BMR Formula">
                <table id="bmr-activity">
                    <tr>
                        <td><strong>Activity Level</strong></td>
                        <td><strong>Multiplier</strong></td>
                    </tr>
                    <tr>
                        <td>Sedentary (little to no exercise)</td>
                        <td>BMR × 1.2</td>
                    </tr>
                    <tr>
                        <td>Lightly active (light exercise 1-3 days a week)</td>
                        <td>BMR × 1.375</td>
                    </tr>
                    <tr>
                        <td>Moderately active (moderate exercise 3-5 days a week)</td>
                        <td>BMR × 1.55</td>
                    </tr>
                    <tr>
                        <td>Very active (intense exercise 6-7 days a week)</td>
                        <td>BMR × 1.725</td>
                    </tr>
                    <tr>
                        <td>Extra active (very intense exercise or physical job)</td>
                        <td>BMR × 1.9</td>
                    </tr>
                </table>
                <p>
                    Whether your goal is to lose weight, gain muscle, or maintain your current physique, calculating your BMR and adjusting it for your activity level is a vital step. This information empowers you to take a strategic approach to your nutrition and exercise regimen, ensuring that your body gets the energy it needs while aligning with your fitness goals.
                </p>
                <p>
                    For an even more accurate understanding of your metabolic rate, consider using advanced methods such as metabolic testing or consulting with a fitness professional who can tailor a plan specific to your needs.
                </p>
            </div>
            <hr>
            <div class="section-info" id="section-tdee">
                <h2>Total Daily Energy Expenditure (TDEE)</h2>
                <p>
                    TDEE represents the total number of calories you burn in a day, including both your basal metabolic rate (BMR) and calories burned through activity. It's an essential metric for determining how much you should eat to maintain, lose, or gain weight.
                </p>
                <p id="title-formula"><strong>Formula to Calculate TDEE</strong></p>
                <img id="tdee-formula" src="../graphics/formulas/tdee-formula.png" alt="BMI Formula - Imperial" title="BMI Formula">
                <table id="tdee-table">
                    <tr>
                        <td><strong>Activity Level</strong></td>
                        <td><strong>Multiplier</strong></td>
                        <td><strong>Description</strong></td>
                    </tr>
                    <tr>
                        <td>Sedentary</td>
                        <td>1.2</td>
                        <td>Little or no exercise</td>
                    </tr>
                    <tr>
                        <td>Lightly Active</td>
                        <td>1.375</td>
                        <td>Light exercise/sports 1-3 days/week</td>
                    </tr>
                    <tr>
                        <td>Moderately Active</td>
                        <td>1.55</td>
                        <td>Moderate exercise/sports 3-5 days/week</td>
                    </tr>
                    <tr>
                        <td>Very Active</td>
                        <td>1.725</td>
                        <td>Hard exercise/sports 6-7 days/week</td>
                    </tr>
                    <tr>
                        <td>Extra Active</td>
                        <td>1.9</td>
                        <td>Very hard exercise, physical job, or training twice a day</td>
                    </tr>
                </table>
            </div>
            <hr>
            <div class="section-info" id="section-protein">
                <h2>Protein</h2>
                <p>
                    Protein is an essential macronutrient that plays a vital role in muscle repair, growth, and overall bodily function. For fitness enthusiasts, getting the right amount of protein is key to optimizing performance and recovery.
                </p>
                <p id="title-formula"><strong>Daily Protein Intake Recommendations</strong></p>
                <table>
                    <tr>
                        <td><strong>Category</strong></td>
                        <td><strong>Protein Recommendation</strong></td>
                    </tr>
                    <tr>
                        <td>Sedentary Individuals</td>
                        <td>0.8 grams per kilogram of body weight</td>
                    </tr>
                    <tr>
                        <td>Active Individuals</td>
                        <td>1.2–2.0 grams per kilogram of body weight</td>
                    </tr>
                    <tr>
                        <td>Strength Training</td>
                        <td>1.6–2.4 grams per kilogram of body weight</td>
                    </tr>
                    <tr>
                        <td>Weight Loss</td>
                        <td>2.0–2.4 grams per kilogram of body weight</td>
                    </tr>
                </table>
                <p id="title-formula"><strong>How to Calculate Protein Intake</strong></p>
                <img id="protein-intake-formula" src="../graphics/formulas/protein-intake-formula.png" alt="BMI Formula - Imperial" title="Protein Intake Formula">            
            </div>
        </div>
    </section>
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
    <script src="./calculator.js"></script>
</body>
</html>
