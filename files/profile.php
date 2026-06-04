<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

// Check if user_id is provided
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

$userId = current_user_id();

// Determine today's weekday in PHP
$today = date("l"); // Returns full day name, e.g., "Monday"

// Fetch workouts for the current day
$stmt = db()->prepare("
    SELECT workout_name AS \"workoutName\", workout_reps AS \"workoutReps\", workout_sets AS \"workoutSets\"
    FROM workouts
    WHERE user_id = :user_id AND workout_day = :workout_day
");
$stmt->execute([
    'user_id' => $userId,
    'workout_day' => $today,
]);

$workouts = $stmt->fetchAll();

echo "<script>const workouts = " . json_encode($workouts) . ";</script>";


// Prepare SQL statement to fetch user data
$stmt = db()->prepare("
    SELECT first_name AS \"firstName\", last_name AS \"lastName\", birthdate, height, weight, activity
    FROM users
    WHERE id = :user_id
");
$stmt->execute(['user_id' => $userId]);
$user = $stmt->fetch();


// Fetch user data
if ($user) {
    // Pass user data to the front end
    echo "<script>const userData = " . json_encode($user) . ";</script>";
} else {
    echo "Error: User not found.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../graphics/logo/logo.png">
    <link rel="stylesheet" href="./profile.css">
    <link rel="stylesheet" href="./register.css">
    <script type="text/javascript">
        const userId = <?php echo json_encode($userId); ?>;
    </script>
    <title>GreekGods | Profile</title>
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
            <!-- <button id="profile-button" onclick="location.reload(); return false;"><img src="../graphics/svg/profile.svg" alt="Profile" title="Profile"></button> -->
            <!-- <span id="profile-name"><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></span> -->
            <button id="logout">LOGOUT</button>
        </div>
    </nav>
    <header>
            <div id="header-welcome" style="box-sizing:border-box;width:100%;padding:10px 30px 0px 30px;display:inline-block;background-color:white;border-top:1px solid lightgray">
                <h3 style="font-size: 3em;text-transform:uppercase;color:black;padding:0px;margin-top:20px;">WELCOME, <?php echo htmlspecialchars($user['firstName']); ?>!</h3>
                <p style="font:500 0.945em/1em 'Trebuchet MS';color:#555;text-align:center">Welcome to GreekGods! Let's start you fitness journey by embracing your body numbers!. Navigate to <a href="./blog.php">Blog</a> for step by step comprehensive fitness instructions.</p>
            </div> 

            <div class="header-container">
            <div class="header-info" id="header-personal-info">
                <h3>Personal Information</h3>
                <p>Name: <span id="name"></span></p>
                <p>Birthday: <span id="birthdate"></span></p>
                <p>Age: <span id="age"></span></p>
                <p>Height: <span id="height"></span></p>
                <p>Weight: <span id="weight"></span></p>
                <p>Body Mass Index (BMI): <span id="bmi"></span></p>
                <p>Classification: <span id="classification"></span></p>
            </div>

            <div class="header-info" id="header-statistics">
                <h3>Body Numbers</h3>
                <p>Basal Metabolic Rate: <span id="bmr"></span></p>
                <p>Total Daily Energy Expenditure: <span id="tdee"></span> </p>
                <p>Maintain Weight: <span id="maintain"></span></p>
                <p>Mid Weight Loss: <span id="mid"></span></p>
                <p>Weight Loss: <span id="weight-loss"></span></p>
                <p>Extreme Weight Loss: <span id="extreme"></span></p>
                <p>Protein Intake: <span id="protein"></span></p>
            </div>

            <div class="header-info" id="header-program">
                <h3>Program</h3>
                <h4 id="weekday-today"></h4>
                <h4 id="date-today"></h4> <!-- Added date display -->
                <div class="split">
                    <table class="workouts">
                        <thead>
                            <tr>
                                <th>Workouts</th>
                                <th>Reps</th>
                                <th>Sets</th>
                            </tr>
                        </thead>
                        <tbody id="workout-table-body">
                            <!-- Rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
                <button onclick="window.location.href='./program.php'">PROCEED TO PROGRAM</button>
            </div>       
        </div> 
        <button id="edit-informations">EDIT INFORMATIONS</button>
    </header>

    <!-----------------DISPLAY:NONE----------------->
    <section>
        <div class="section-container">
            <div class="form">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" >

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">

            <label for="first-name">First Name</label>
            <input type="text" id="first-name" name="first-name">

            <label for="last-name">Last Name</label>
            <input type="text" id="last-name" name="last-name">
                
            <label for="birthdate">Birthdate</label>
            <input type="text" id="birthdate-change" name="birthdate">

            <label for="height">Height</label>
            <div class="section-metrics">
                <input id="height-change" type="text" name="height" step="0.01"> 
                <select name="heightMetrics" id="heightMetric">
                    <option value="cm">.cm</option>
                    <option value="in">.in</option>
                    <option value="m">.m</option>
                    <option value="ft">.ft</option>
                </select>
            </div>

            <label for="weight">Weight</label>
            <div class="section-metrics">
                <input id="weight-change" type="text" name="weight" step="0.01">
                <select name="weightMetrics" id="weightMetric">
                    <option value="lb">.lb</option>
                    <option value="kg">.kg</option>
                </select>
            </div>

            <div class="section">
                <label for="activity" data-target="#bmr-activity">Activity<span src="../graphics/svg/info-black.svg"></span></label>
                <select name="activity" id="activity" required> 
                    <option value="" disabled selected></option>
                    <option value="sedentary">Sedentary: little or no exercise</option>
                    <option value="light">Light: exercise 1-3 times/week</option>
                    <option value="moderate">Moderate: exercise 3-5 times/week</option>
                    <option value="active">Active: intense exercise 6-7 times/week</option>
                    <option value="very_active">Very Active: very intense exercise daily, or physical job</option>
                </select> 
            </div>    
            <button type="button" id="updateButton">Update</button>
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
    <script src="./profile.js"></script>
    <script>
         document.addEventListener("DOMContentLoaded", () => {
        // Display today's weekday
        const today = new Date();
        const weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        document.getElementById("weekday-today").textContent = weekdays[today.getDay()];

        // Display today's date in MM, DD, YYYY format
        document.getElementById("date-today").textContent = today.toLocaleDateString("en-US", {
            month: "2-digit",
            day: "2-digit",
            year: "numeric",
        });

        // Populate the workout table
        const workoutTableBody = document.getElementById("workout-table-body");
        workoutTableBody.innerHTML = ""; // Clear previous rows if any

        if (workouts.length > 0) {
            workouts.forEach(workout => {
                const row = document.createElement("tr");

                const nameCell = document.createElement("td");
                nameCell.textContent = workout.workoutName;

                const repsCell = document.createElement("td");
                repsCell.textContent = workout.workoutReps;

                const setsCell = document.createElement("td");
                setsCell.textContent = workout.workoutSets;

                row.appendChild(nameCell);
                row.appendChild(repsCell);
                row.appendChild(setsCell);

                workoutTableBody.appendChild(row);
            });
        } else {
            const noWorkoutRow = document.createElement("tr");
            const noWorkoutCell = document.createElement("td");
            noWorkoutCell.textContent = "No workouts scheduled for today.";
            noWorkoutCell.colSpan = 3; // Span across all columns
            noWorkoutCell.style.textAlign = "center"; // Center the text
            noWorkoutRow.appendChild(noWorkoutCell);
            workoutTableBody.appendChild(noWorkoutRow);
        }
    });
        
        function calculateDerivedData(data) {
            const fullName = `${data.firstName} ${data.lastName}`;
            const birthdate = new Date(data.birthdate);
            const today = new Date();

            // Calculate Age
            let age = today.getFullYear() - birthdate.getFullYear();
            if (
                today.getMonth() < birthdate.getMonth() || 
                (today.getMonth() === birthdate.getMonth() && today.getDate() < birthdate.getDate())
            ) {
                age--;
            }

            // BMI Calculation
            const heightInMeters = data.height;
            const bmi = (data.weight / (heightInMeters * heightInMeters)).toFixed(2);
            let classification = "";
            if (bmi < 18.5) classification = "Underweight";
            else if (bmi < 25) classification = "Normal weight";
            else if (bmi < 30) classification = "Overweight";
            else classification = "Obese";

            // BMR Calculation (Mifflin-St Jeor formula)
            const bmr = 10 * data.weight + 6.25 * (data.height * 100) - 5 * age + (data.gender === "male" ? 5 : -161);

            // Activity Factor
            const activityFactors = {
                sedentary: 1.2,
                light: 1.375,
                moderate: 1.55,
                active: 1.725,
                very_active: 1.9,
            };
            const activityFactor = activityFactors[data.activity] || 1.2;

            // TDEE Calculation
            const tdee = bmr * activityFactor;

            // Caloric Needs
            const maintain = tdee;
            const midWeightLoss = tdee - 250;
            const weightLoss = tdee - 500;
            const extremeWeightLoss = tdee - 1000;

            // Protein Intake
            const protein = (data.weight * 1.6).toFixed(0);

            // Update HTML Elements
            document.getElementById("name").textContent = fullName;
            document.getElementById("birthdate").textContent = data.birthdate;
            document.getElementById("age").textContent = age;
            document.getElementById("height").textContent = `${heightInMeters.toFixed(2)} m`;
            document.getElementById("weight").textContent = `${data.weight.toFixed(2)} kg`;
            document.getElementById("bmi").textContent = bmi;
            document.getElementById("classification").textContent = classification;

            document.getElementById("bmr").textContent = `${bmr.toFixed(2)} kcal/day`;
            document.getElementById("tdee").textContent = `${tdee.toFixed(2)} kcal/day`;
            document.getElementById("maintain").textContent = `${maintain.toFixed(2)} kcal/day`;
            document.getElementById("mid").textContent = `${midWeightLoss.toFixed(2)} kcal/day`;
            document.getElementById("weight-loss").textContent = `${weightLoss.toFixed(2)} kcal/day`;
            document.getElementById("extreme").textContent = `${extremeWeightLoss.toFixed(2)} kcal/day`;
            document.getElementById("protein").textContent = `${protein} g/day`;
        }

        // Initialize user data and calculate derived values
        calculateDerivedData(userData);

    </script>
    <script src="../index.js" defer></script>
</body>
</html>
