document.querySelector('.header-calculator').addEventListener('submit', function (event) {
    event.preventDefault();

    // Validation for required fields
    if (!validateForm()) {
        return; // Stop the form submission if validation fails
    } else {
        document.getElementById('main-info').scrollIntoView({
            behavior: 'smooth', // Smooth scrolling
            block: 'start', // Align at the start of the section
        });
    }

    // Fetch input values
    const age = document.getElementById('age').value.trim();
    const gender = document.querySelector('input[name="gender"]:checked')?.value;
    const activity = document.getElementById('activity').value;
    const formula = document.getElementById('formula').value;

    // Display age and gender
    document.getElementById('age-result').textContent = age;
    document.getElementById('gender-result').textContent = gender === 'male' ? 'Male' : 'Female';

    // Convert height and weight to metric units
    const { finalHeight, finalWeight } = convertMetric();

    // Update height and weight in the results
    document.getElementById('height-result').textContent = finalHeight.toFixed(2) + " m";
    document.getElementById('weight-result').textContent = finalWeight.toFixed(2) + " kg";

    // Calculate BMI and classification
    calculateBMI(finalHeight, finalWeight);

    // Calculate additional outputs
    calculateCalories(formula, finalHeight, finalWeight, age, gender, activity);
    calculateProtein(finalWeight);
});

function validateForm() {
    const age = document.getElementById('age').value.trim();
    const gender = document.querySelector('input[name="gender"]:checked');
    const height = document.getElementById('height').value.trim();
    const weight = document.getElementById('weight').value.trim();
    const activity = document.getElementById('activity').value;
    const formula = document.getElementById('formula').value;

    let isValid = true;
    const missingFields = [];

    if (!age || isNaN(age) || age <= 0) {
        isValid = false;
        missingFields.push("valid age");
    }

    if (!gender) {
        isValid = false;
        missingFields.push("gender");
    }

    if (!height || isNaN(height) || height <= 0) {
        isValid = false;
        missingFields.push("valid height");
    }

    if (!weight || isNaN(weight) || weight <= 0) {
        isValid = false;
        missingFields.push("valid weight");
    }

    if (!activity) {
        isValid = false;
        missingFields.push("activity level");
    }

    if (!formula) {
        isValid = false;
        missingFields.push("formula selection");
    }

    // If using the Katch-McArdle formula, validate body fat percentage
    if (formula === "katch_mcardle") {
        const bodyFat = document.getElementById('sliderValue').value.trim();
        if (!bodyFat || isNaN(bodyFat) || bodyFat < 0 || bodyFat > 100) {
            isValid = false;
            missingFields.push("valid body fat percentage");
        }
    }

    if (!isValid) {
        notify("warning", `Please complete: ${missingFields.join(", ")}.`);
    }

    return isValid;
}

function notify(type, message) {
    const toaster = window.GreekGodsToast;

    if (message && typeof toaster?.[type] === "function") {
        toaster[type](message);
    }
}

function calculateBMI(height, weight) {
    if (isNaN(height) || isNaN(weight) || height <= 0 || weight <= 0) {
        document.getElementById("bmi-result").textContent = "Invalid input";
        document.getElementById("classification-result").textContent = "Invalid input";
        return;
    }

    const bmi = weight / (height * height);
    let category = "";
    if (bmi < 18.5) {
        category = "Underweight";
    } else if (bmi >= 18.5 && bmi < 24.9) {
        category = "Normal";
    } else if (bmi >= 25 && bmi < 29.9) {
        category = "Overweight";
    } else {
        category = "Obese";
    }

    // Update BMI and classification in the UI
    document.getElementById("bmi-result").textContent = bmi.toFixed(2);
    document.getElementById("classification-result").textContent = category;
}

function convertMetric() {
    const height = parseFloat(document.getElementById('height').value);
    const weight = parseFloat(document.getElementById('weight').value);

    if (isNaN(height) || isNaN(weight)) {
        document.getElementById("height-result").textContent = "Invalid input";
        document.getElementById("weight-result").textContent = "Invalid input";
        return;
    }

    const heightMetric = document.getElementById('heightMetric').value;
    const weightMetric = document.getElementById('weightMetric').value;

    let finalHeight;
    let finalWeight;

    if (heightMetric === "cm") {
        finalHeight = height / 100;
    } else if (heightMetric === "m") { 
        finalHeight = height;
    } else if (heightMetric === "in") {
        finalHeight = height * 0.0254;
    } else if (heightMetric === "ft") {
        finalHeight = height * 0.3048;
    } else {
        finalHeight = height;
    }

    if (weightMetric === "kg") {
        finalWeight = weight;
    } else if (weightMetric === "lb") {
        finalWeight = weight * 0.453592;
    } else {
        finalWeight = weight;
    }

    document.getElementById("height-result").textContent = finalHeight.toFixed(2);
    document.getElementById("weight-result").textContent = finalWeight.toFixed(2);

    return { finalHeight, finalWeight };
}

function convertActivityFactor() {
    const activityLevel = document.getElementById('activity').value;
    const activityFactors = {
        sedentary: 1.2,
        light: 1.375,
        moderate: 1.55,
        active: 1.725,
        very_active: 1.9,
    };

    const activityFactor = activityFactors[activityLevel];
    document.getElementById("activity-result").textContent = activityLevel;

    return activityFactor; 
}

function calculateBMR(height, weight, activityFactor) {
    const age = parseInt(document.getElementById('age').value);
    const gender = document.querySelector('input[name="gender"]:checked')?.value;
    const formula = document.getElementById('formula').value;

    if (isNaN(age) || isNaN(height) || isNaN(weight) || !gender || !formula) {
        document.getElementById("maintain-weight").textContent = "Invalid input";
        document.getElementById("mid-weight-loss").textContent = "Invalid input";
        document.getElementById("weight-loss").textContent = "Invalid input";
        document.getElementById("extreme-weight-loss").textContent = "Invalid input";
        return;
    }

    let bmr;

    if (formula === "mifflin_st_jeor") {
        // Mifflin St. Jeor Formula
        if (gender === "male") {
            bmr = 10 * weight + 6.25 * height * 100 - 5 * age + 5; // Height in meters converted to cm
        } else if (gender === "female") {
            bmr = 10 * weight + 6.25 * height * 100 - 5 * age - 161;
        }
    } else if (formula === "revised_harris_benedict") {
        // Revised Harris-Benedict Formula
        if (gender === "male") {
            bmr = 88.362 + (13.397 * weight) + (4.799 * height * 100) - (5.677 * age);
        } else if (gender === "female") {
            bmr = 447.593 + (9.247 * weight) + (3.098 * height * 100) - (4.330 * age);
        }
    } else if (formula === "katch_mcardle") {
        // Katch-McArdle Formula
        const bodyFatPercentage = parseFloat(document.getElementById('sliderValue').value);
        if (isNaN(bodyFatPercentage) || bodyFatPercentage < 0 || bodyFatPercentage > 100) {
            document.getElementById("maintain-weight").textContent = "Invalid input";
            document.getElementById("mid-weight-loss").textContent = "Invalid input";
            document.getElementById("weight-loss").textContent = "Invalid input";
            document.getElementById("extreme-weight-loss").textContent = "Invalid input";
            return;
        }
        const leanBodyMass = weight * (1 - bodyFatPercentage / 100);
        bmr = 370 + (21.6 * leanBodyMass);
    }

    if (!bmr) {
        document.getElementById("maintain-weight").textContent = "Calculation failed";
        return;
    }

    const caloriesPerDay = bmr * activityFactor;

    // Update the results in the div
    document.getElementById("bmr").textContent = bmr.toFixed(2) + " calories/day";
    document.getElementById("maintain-weight").textContent = caloriesPerDay.toFixed(2) + " calories/day";
    document.getElementById("mid-weight-loss").textContent = (caloriesPerDay - 500).toFixed(2) + " calories/day";
    document.getElementById("weight-loss").textContent = (caloriesPerDay - 750).toFixed(2) + " calories/day";
    document.getElementById("extreme-weight-loss").textContent = (caloriesPerDay - 1000).toFixed(2) + " calories/day";
}

function calculateCalories() {
    const { finalHeight, finalWeight } = convertMetric();
    const activityFactor = convertActivityFactor();
    calculateBMR(finalHeight, finalWeight, activityFactor);
}

function calculateProtein() {
    const { finalWeight } = convertMetric();

    let activityLevel = document.getElementById('activity').value; // Activity level

    if (!activityLevel) {
        activityLevel = "sedentary"; 
        document.getElementById('activity-result').textContent = "Sedentary (default)";
    }

    if (isNaN(finalWeight) || finalWeight <= 0) {
        document.getElementById("protein-intake").textContent = "Invalid input";
        return;
    }

    let proteinFactor;

    switch (activityLevel) {
        case "sedentary":
            proteinFactor = 0.8;
            break;
        case "light":
            proteinFactor = 1.2;
            break;
        case "moderate":
            proteinFactor = 1.4;
            break;
        case "active":
            proteinFactor = 1.6;
            break;
        case "very_active":
            proteinFactor = 2.2;
            break;
        default:
            proteinFactor = 0.8; // Fallback to sedentary if activity level is invalid
            break;
    }

    const proteinIntake = finalWeight * proteinFactor;
    document.getElementById("protein-intake").textContent = proteinIntake.toFixed(2) + " grams/day";
}

// McArdle Formula
function toggleKatchMcardleFormula() {
    const formulaSelect = document.getElementById('formula');
    const katchMcardleContainer = document.getElementById('katchMcardleContainer');
    
    formulaSelect.addEventListener('change', function () {
        if (formulaSelect.value === 'katch_mcardle') {
            katchMcardleContainer.style.display = 'flex'; 
        } else {
            katchMcardleContainer.style.display = 'none'; 
        }
    });

    const slider = document.getElementById('bodyFat');
    const sliderValue = document.getElementById('sliderValue');
    
    slider.addEventListener('input', function () {
        sliderValue.value = slider.value; 
    });

    sliderValue.addEventListener('input', function () {
        slider.value = sliderValue.value; 
    });
}
toggleKatchMcardleFormula();

//------------------------------------------------------------
// Select all labels with a data-target attribute
const labels = document.querySelectorAll('label[data-target]');

labels.forEach(label => {
    label.addEventListener('click', (event) => {
        // Prevent default behavior in case of future anchor tags
        event.preventDefault();

        // Get the target section's ID from the data attribute
        const targetId = label.getAttribute('data-target');
        const targetElement = document.querySelector(targetId);

        // Scroll to the target section
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth', // Smooth scrolling
                block: 'start', // Align at the start of the section
            });
        }
    });

    const tableHeaders = document.querySelectorAll('.main-bmi th, .main-calorie th, .main-protein th');

    tableHeaders.forEach(th => {
    th.addEventListener('click', (event) => {
        event.preventDefault(); // Prevent default behavior

        // Identify the target section based on the table header class
        let targetId;
        if (th.closest('.main-bmi')) {
            targetId = '#section-bmi';
        } else if (th.closest('.main-calorie')) {
            targetId = '#section-tdee';
        } else if (th.closest('.main-protein')) {
            targetId = '#section-protein';
        }

        // Scroll to the target section
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth', // Smooth scrolling
                block: 'start' // Align at the start of the section
            });
        }
    });
});
});
