<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: ./login.php");
//     exit();
// }

$userId = current_user_id();

// Fetch user details
$userName = fetch_user_name($userId);
$firstName = $userName['firstName'];
$lastName = $userName['lastName'];

// Check if a program already exists for the user
$existingProgram = false;
$existingProgramDetails = null;  // Initialize this as null

if ($userId) {
    // Check if there's an existing program for the user
    $stmt = db()->prepare("SELECT program, schedule FROM programs WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $existingProgramDetails = $stmt->fetch();

    // If a program exists, fetch the details
    if ($existingProgramDetails) {
        $existingProgram = true;
    }
}

// Fetch user workouts
$workoutData = [];
if ($userId) {
    $stmt = db()->prepare("
        SELECT
            workout_name AS \"workoutName\",
            workout_reps AS \"workoutReps\",
            workout_sets AS \"workoutSets\",
            workout_day AS \"workoutDay\"
        FROM workouts
        WHERE user_id = :user_id
    ");
    $stmt->execute(['user_id' => $userId]);
    $workoutData = $stmt->fetchAll();
}

// Handle the POST request when saving the program
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['workouts'], $_POST['split'], $_POST['days'])) {
    $workouts = json_decode($_POST['workouts'], true);
    $split = $_POST['split'];
    $days = $_POST['days'];

    // Start transaction
    $pdo = db();
    $pdo->beginTransaction();

    try {
        // Delete existing program and workouts
        $stmt = $pdo->prepare("DELETE FROM programs WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);

        $stmt = $pdo->prepare("DELETE FROM workouts WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);

        // Save new program split and days in the program table
        $stmt = $pdo->prepare("
            INSERT INTO programs (user_id, program, schedule)
            VALUES (:user_id, :program, :schedule)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'program' => $split,
            'schedule' => $days,
        ]);

        // Save new workouts in the workouts table
        $stmt = $pdo->prepare("
            INSERT INTO workouts (user_id, workout_name, workout_reps, workout_sets, workout_day)
            VALUES (:user_id, :workout_name, :workout_reps, :workout_sets, :workout_day)
        ");
        foreach ($workouts as $workout) {
            if (isset($workout['workoutDay'], $workout['workoutName'], $workout['workoutSets'], $workout['workoutReps'])) {
                $stmt->execute([
                    'user_id' => $userId,
                    'workout_name' => $workout['workoutName'],
                    'workout_reps' => (int) $workout['workoutReps'],
                    'workout_sets' => (int) $workout['workoutSets'],
                    'workout_day' => $workout['workoutDay'],
                ]);
            }
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Program saved successfully!']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to save program: ' . $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../graphics/logo/logo.png">
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="./program.css">
    <script type="text/javascript">
        const userId = <?php echo json_encode($userId); ?>;
        const workoutData = <?php echo json_encode($workoutData); ?>;
        const existingProgram = <?php echo json_encode($existingProgram); ?>;
        const existingProgramDetails = <?php echo json_encode($existingProgramDetails); ?>;
    </script>
    <title>GreekGods | Program</title>
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
            <li><a href="./program.php" onclick="location.reload(); return false;">PROGRAM</a></li>
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
        <div class="header-container">
            <div class="header-sections">
                <h1 id="header-welcome-message">HI, <?php echo htmlspecialchars($firstName); ?>!</h1>
                <p>Let's start to plan your weekly workouts.</p>
            </div>
        </div>
    </header>
    <section>
        <div class="section-container">
            <div class="section-select-container">
                <div class="section-select">
                    <label for="workout-splits">Workout Splits</label>
                    <select name="header-workout-splits" id="workout-splits">
                        <option value="" disabled selected>Select Workout Split</option>
                        <option value="full-body">Full Body</option>
                        <option value="upper-lower">Upper Lower Split</option>
                        <option value="push-pull-legs">Push Pull Legs Split</option>
                        <option value="push-pull">Push Pull Split</option>
                        <option value="bro-split">Body Part Workout Split (Bro Split)</option>
                    </select>
                </div>
                <div class="section-select">
                    <label for="workout-splits-options">Days</label>
                    <select name="header-workout-splits-options" id="workout-splits-options">
                        <option value="" disabled selected>Select Days</option>
                    </select>
                </div>
            </div>
            <div class="section-save">
                <button id="change-program">CHANGE PROGRAM</button>
                <button id="save-program">SAVE PROGRAM</button>
            </div>
        </div>
    </section>
    <main>
        <div class="main-container">                
            <div class="main-days" id="monday">
                <h4>Monday</h4>
                <div class="split">
                    <h5 class="split-name"></h5>
                    <div class="workouts"></div>
                    <button class="add"></button>
                </div>
            </div>
            <div class="main-days" id="tuesday">
                <h4>Tuesday</h4>
                <div class="split">
                    <h5 class="split-name"></h5>
                    <div class="workouts"></div>
                    <button class="add"></button>
                </div>
            </div>
            <div class="main-days" id="wednesday">
                <h4>Wednesday</h4>
                <div class="split">
                    <h5 class="split-name"></h5>
                    <div class="workouts"></div>
                    <button class="add"></button>
                </div>
            </div>
            <div class="main-days" id="thursday">
                <h4>Thursday</h4>
                <div class="split">
                    <h5 class="split-name"></h5>
                    <div class="workouts"></div>
                    <button class="add"></button>
                </div>
            </div>
            <div class="main-days" id="friday">
                <h4>Friday</h4>
                <div class="split">
                    <h5 class="split-name"></h5>
                    <div class="workouts"></div>
                    <button class="add"></button>
                </div>
            </div>
            <div class="main-days" id="saturday">
                <h4>Saturday</h4>
                <div class="split">
                    <h5 class="split-name"></h5>
                    <div class="workouts"></div>
                    <button class="add"></button>
                </div>
            </div>
            <div class="main-days" id="sunday">
                <h4>Sunday</h4>
                <div class="split">
                    <h5 class="split-name"></h5>
                    <div class="workouts"></div>
                    <button class="add"></button>
                </div>
            </div>
        </div>
    </main>

    <!-- DISPLAY:NONE -->
    <form action="" class="add-workout">
        <label for="form-workout">Workout</label>
        <input type="text" id="workout-name">
        <div class="autocomplete-suggestions"></div>
        <label for="form-sets">Sets</label>
        <input type="number" min="1" max="50" id="workout-sets">
        <label for="form-reps">Reps</label>
        <input type="number" min="1" max="50" id="workout-reps">
        <button id="form-add">Add</button>
    </form>
    
    <form class="workouts-div">
        <input type="text" class="workout-name" readonly>
        <div class="number">
            <input type="number" min="1" max="50" class="workout-sets" readonly>
            <input type="number" min="1" max="50" class="workout-reps" readonly>
        </div>
        <button class="delete"></button>
    </form>
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
    <script src="./program.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const workoutSplits = document.getElementById("workout-splits");
            const workoutSplitsOptions = document.getElementById("workout-splits-options");
            const saveButton = document.getElementById("save-program");
            const changeButton = document.getElementById("change-program");

            const populateScheduleDropdown = (splitType) => {
                workoutSplitsOptions.innerHTML = '<option value="" disabled selected>Select Days</option>';

                if (workoutSchedules[splitType]) {
                    Object.keys(workoutSchedules[splitType]).forEach(schedule => {
                        const option = document.createElement("option");
                        option.value = schedule;
                        option.textContent = schedule;
                        workoutSplitsOptions.appendChild(option);
                    });
                }
            };

            const setExistingProgramDetails = (existingProgramDetails) => {
                if (existingProgramDetails) {
                    const { program, schedule } = existingProgramDetails;

                    // Set the workout split dropdown
                    workoutSplits.value = program;

                    // Populate the days dropdown for the selected program
                    populateScheduleDropdown(program);

                    workoutSplitsOptions.disabled = true;
                    workoutSplits.disabled = true;

                    // Set the schedule value once options are loaded
                    setTimeout(() => {
                        const scheduleExists = [...workoutSplitsOptions.options].some(
                            option => option.value === schedule
                        );

                        if (scheduleExists) {
                            workoutSplitsOptions.value = schedule;
                            // Trigger the workout plan update after setting the schedule
                            updateWorkoutPlan();
                        } else {
                            console.warn(`Schedule "${schedule}" not found in dropdown options.`);
                        }
                    }, 0);
                }
            };

            // If there's an existing program, set the values
            setExistingProgramDetails(existingProgramDetails);


            // Event listeners for dynamic dropdown population
            workoutSplits.addEventListener("change", function () {
                const splitType = this.value;
                populateScheduleDropdown(splitType);
            });

            function updateWorkoutPlan() {
                const splitType = workoutSplits.value; // Get the selected workout split
                const selectedSchedule = workoutSplitsOptions.value;  // Get the selected schedule
                const schedule = workoutSchedules[splitType] && workoutSchedules[splitType][selectedSchedule];

                if (schedule) {
                    // Iterate over each day and update the workout name
                    Object.entries(schedule).forEach(([day, workout]) => {
                        const dayElement = document.getElementById(day.toLowerCase());  // Ensure day ID is correct in HTML (lowercase)
                        if (dayElement) {
                            const splitName = dayElement.querySelector(".split-name");
                            if (splitName) {
                                splitName.textContent = workout;  // Update the split name for the day
                            } else {
                                console.warn(`No .split-name element found for ${day}`);
                            }

                            // Disable the main-days container and pointer events if workout is "Rest"
                            const mainDayElement = document.querySelector(`#${day.toLowerCase()}.main-days`);
                            if (mainDayElement && workout === "Rest") {
                                mainDayElement.style.pointerEvents = "none";
                                mainDayElement.style.opacity = "0.5"; // Optionally, reduce opacity to indicate disabled state
                            } else if (mainDayElement && workout !== "Rest") {
                                mainDayElement.style.pointerEvents = "auto";
                                mainDayElement.style.opacity = "1"; // Reset opacity when not "Rest"
                            }
                        } else {
                            console.warn(`No element found for day: ${day}`);
                        }
                    });
                } else {
                    console.warn(`No schedule found for ${splitType} and ${selectedSchedule}`);
                }
            }

            // Populate workouts from fetched data
            workoutData.forEach(workout => {
                const { workoutDay, workoutName, workoutReps, workoutSets } = workout;
                const dayElement = document.getElementById(workoutDay);

                if (dayElement) {
                    const workoutsDiv = dayElement.querySelector('.workouts');

                    // Create a new workout entry
                    const workoutDiv = document.createElement('form');
                    workoutDiv.className = 'workouts-div';

                    workoutDiv.innerHTML = `
                        <input type="text" class="workout-name" value="${workoutName}" readonly>
                        <div class="number">
                            <input type="text" class="workout-sets" value="${workoutSets} x sets" readonly>
                            <input type="text" class="workout-reps" value="${workoutReps} x reps" readonly>
                        </div>
                        <button class="delete"></button>
                    `;

                    workoutsDiv.appendChild(workoutDiv);
                }
            });
        });

        document.getElementById('save-program').addEventListener('click', async () => {
            const split = document.getElementById('workout-splits').value;
            const days = document.getElementById('workout-splits-options').value;

            if (!split || !days) {
                alert("Please select a workout split and days.");
                return;
            }

            const mainDays = document.querySelectorAll('.main-days');
            const workouts = [];

            mainDays.forEach(day => {
                const workoutDay = day.id;
                const workoutDivs = day.querySelectorAll('.workouts-div');

                workoutDivs.forEach(workoutDiv => {
                    const workoutName = workoutDiv.querySelector('.workout-name')?.value.trim();
                    const workoutSets = workoutDiv.querySelector('.workout-sets')?.value;
                    const workoutReps = workoutDiv.querySelector('.workout-reps')?.value;

                    if (workoutName && workoutSets && workoutReps) {
                        workouts.push({
                            workoutDay,
                            workoutName,
                            workoutSets,
                            workoutReps
                        });
                    }
                });
            });

            if (workouts.length > 0) {
                const response = await fetch("", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `workouts=${JSON.stringify(workouts)}&split=${encodeURIComponent(split)}&days=${encodeURIComponent(days)}`
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);

                    // Trigger the workout plan update to display the schedule
                    updateWorkoutPlan();  // This will manually update the .split-name elements

                    // Disable dropdowns after saving (if needed)
                    workoutSplits.disabled = true;
                    workoutSplitsOptions.disabled = true;

                    // Optionally, reload the page or refresh data (if required)
                    // location.reload();
                } else {
                    alert('Failed to save program: ' + result.message);
                }
            } else {
                alert("Please add some workouts first.");
            }   
        });

        document.addEventListener("DOMContentLoaded", () => {
            const changeButton = document.getElementById("change-program");

            // When "CHANGE PROGRAM" is clicked, show a confirmation prompt
            changeButton.addEventListener("click", async () => {
                // Check if there is anything to delete
                if (!existingProgram || !existingProgramDetails || workoutData.length === 0) {
                    alert("You dont have any a existing program yet.");
                    return;
                }

                const confirmation = confirm("Are you sure you want to change program? If yes, all of your workouts in your current program will be deleted.");
                
                if (confirmation) {
                    try {
                        // Send a request to delete the program and workouts from the database
                        const response = await fetch("./delete_program.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: `userId=${encodeURIComponent(userId)}`
                        });

                        const result = await response.json();

                        if (result.success) {
                            alert("Program and workouts have been deleted.");
                            location.reload();  // Reload the page to reflect the changes
                        } else {
                            alert("Failed to delete program: " + result.message);
                        }
                    } catch (error) {
                        alert("Error: " + error.message);
                    }
                }
            });
        });

        
    </script>
</body>
</html>
