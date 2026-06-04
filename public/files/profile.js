const ACTIVITY_FACTORS = {
    sedentary: 1.2,
    light: 1.375,
    moderate: 1.55,
    active: 1.725,
    very_active: 1.9,
};

const ACTIVITY_LABELS = {
    sedentary: "Sedentary",
    light: "Light",
    moderate: "Moderate",
    active: "Active",
    very_active: "Very active",
};

const SEX_LABELS = {
    male: "Male",
    female: "Female",
    prefer_not_to_say: "Prefer not to say",
};

let currentProfile = typeof profileState !== "undefined"
    ? profileState
    : {
        user: typeof userData !== "undefined" ? userData : {},
        metrics: {},
    };

document.addEventListener("DOMContentLoaded", () => {
    renderDashboard();
    renderTodaysWorkouts(typeof workouts !== "undefined" ? workouts : []);
    populateSettingsForms();
    setupLogout();
    setupSettingsToggles();
    setupProfileForm("account-settings-form");
    setupProfileForm("body-settings-form");
    setupProfileForm("security-settings-form");
});

function setupLogout() {
    const logoutButton = document.getElementById("logout");

    if (!logoutButton) {
        return;
    }

    logoutButton.addEventListener("click", async () => {
        const confirmed = await confirmAction("Are you sure you want to log out?", {
            title: "Log out",
            confirmLabel: "Log out",
        });

        if (confirmed) {
            window.location.href = "/logout";
        }
    });
}

function setupSettingsToggles() {
    document.querySelectorAll("[data-settings-toggle]").forEach((button) => {
        button.addEventListener("click", () => {
            const form = document.getElementById(button.dataset.settingsToggle);

            if (!form) {
                return;
            }

            const shouldOpen = form.hidden;
            form.hidden = !shouldOpen;
            button.setAttribute("aria-expanded", String(shouldOpen));

            if (shouldOpen) {
                clearFormFeedback(form);
                populateSettingsForms();
                const firstInput = form.querySelector("input, select");
                firstInput?.focus();
            }
        });
    });

    document.querySelectorAll("[data-settings-cancel]").forEach((button) => {
        button.addEventListener("click", () => {
            const form = document.getElementById(button.dataset.settingsCancel);

            if (!form) {
                return;
            }

            closeSettingsForm(form);
            populateSettingsForms();
        });
    });
}

function setupProfileForm(formId) {
    const form = document.getElementById(formId);

    if (!form) {
        return;
    }

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        clearFormFeedback(form);
        const payload = formToPayload(form);
        setFormLoading(form, true);

        try {
            const response = await fetch(form.action, {
                method: "PATCH",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
                },
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    renderFormErrors(form, data.errors);
                    setFormStatus(form, "Please review the highlighted fields.", true);
                    notify("warning", "Please review the highlighted fields.");
                    return;
                }

                setFormStatus(form, data.message || "Unable to save changes.", true);
                notify("error", data.message || "Unable to save changes.");
                return;
            }

            if (data.user && data.metrics) {
                currentProfile = {
                    user: data.user,
                    metrics: data.metrics,
                };
                renderDashboard();
                populateSettingsForms();
            }

            if (formId === "security-settings-form") {
                form.reset();
            }

            setFormStatus(form, data.message || "Saved.");
            notify("success", data.message || "Saved.");
        } catch (error) {
            setFormStatus(form, "Network error. Please try again.", true);
            notify("error", "Network error. Please try again.");
        } finally {
            setFormLoading(form, false);
        }
    });
}

function formToPayload(form) {
    const payload = {};
    const formData = new FormData(form);

    formData.forEach((value, key) => {
        if (key === "_token" || key === "_method") {
            return;
        }

        payload[key] = typeof value === "string" ? value.trim() : value;
    });

    return payload;
}

function renderDashboard() {
    const user = currentProfile.user || {};
    const metrics = Object.keys(currentProfile.metrics || {}).length > 0
        ? currentProfile.metrics
        : deriveMetrics(user);

    const fullName = user.fullName || `${user.firstName || ""} ${user.lastName || ""}`.trim() || "Not set";
    const activityLabel = user.activityLabel || ACTIVITY_LABELS[user.activity] || "Not set";
    const sexLabel = user.sexLabel || SEX_LABELS[user.sex] || "Not set";

    setText("hero-first-name", user.firstName || "there");
    setText("hero-summary", `${fullName} - ${user.email || "No email"}`);
    setText("height-summary", user.heightDisplay || formatMeasurement(user.height, 2, "m"));
    setText("weight-summary", user.weightDisplay || formatMeasurement(user.weight, 2, "kg"));
    setText("age-summary", user.age ?? "--");
    setText("birthdate-summary", user.birthdate || "--");
    setText("activity-summary", activityLabel);
    setText("sex-summary-stat", sexLabel);

    setText("bmi-value", formatNumber(metrics.bmi, 2));
    setText("bmi-category", metrics.bmiCategory || "Not available");
    setText("bmi-status", metrics.bmiStatus || "Add height and weight to calculate BMI.");
    document.querySelector(".bmi-gauge")?.style.setProperty("--bmi-position", `${metrics.bmiPercent || 0}%`);

    setText("sex-summary", sexLabel);
    setText("bmr-value", formatCalories(metrics.canEstimateCalories ? metrics.bmr : null));
    setText("tdee-value", formatCalories(metrics.canEstimateCalories ? metrics.tdee : null));
    setText("maintain-value", formatCalories(metrics.canEstimateCalories ? metrics.maintenanceCalories : null));
    setText("mild-deficit-value", formatCalories(metrics.canEstimateCalories ? metrics.mildDeficitCalories : null));
    setText("weight-loss-value", formatCalories(metrics.canEstimateCalories ? metrics.weightLossCalories : null));
    setText("protein-value", formatProtein(metrics.proteinGrams));
    toggleHidden("calorie-estimate-note", Boolean(metrics.canEstimateCalories));

    setText("account-settings-summary", `${fullName} - ${user.email || "No email"}`);
    setText("body-settings-summary", `${user.heightDisplay || formatMeasurement(user.height, 2, "m")} - ${user.weightDisplay || formatMeasurement(user.weight, 2, "kg")} - ${activityLabel}`);
}

function populateSettingsForms() {
    const user = currentProfile.user || {};

    setValue("account-first-name", user.firstName || "");
    setValue("account-last-name", user.lastName || "");
    setValue("account-email", user.email || "");
    setValue("body-birthdate", user.birthdate || "");
    setValue("body-sex", user.sex || "");
    setValue("body-height", user.heightValue ?? user.height ?? "");
    setValue("body-height-unit", user.heightUnit || "m");
    setValue("body-weight", user.weightValue ?? user.weight ?? "");
    setValue("body-weight-unit", user.weightUnit || "kg");
    setValue("body-activity", user.activity || "sedentary");
}

function renderTodaysWorkouts(todayWorkouts) {
    const today = new Date();

    setText("weekday-today", today.toLocaleDateString("en-US", { weekday: "long" }));
    setText("date-today", today.toLocaleDateString("en-US", {
        month: "short",
        day: "2-digit",
        year: "numeric",
    }));

    const workoutTableBody = document.getElementById("workout-table-body");

    if (!workoutTableBody) {
        return;
    }

    workoutTableBody.innerHTML = "";

    if (!Array.isArray(todayWorkouts) || todayWorkouts.length === 0) {
        const emptyRow = document.createElement("tr");
        const emptyCell = document.createElement("td");
        emptyCell.colSpan = 3;
        emptyCell.textContent = "No workouts scheduled for today.";
        emptyRow.appendChild(emptyCell);
        workoutTableBody.appendChild(emptyRow);
        return;
    }

    todayWorkouts.forEach((workout) => {
        const row = document.createElement("tr");

        [workout.workoutName, workout.workoutFocus, formatWorkoutVolume(workout)].forEach((value) => {
            const cell = document.createElement("td");
            cell.textContent = value ?? "--";
            row.appendChild(cell);
        });

        workoutTableBody.appendChild(row);
    });
}

function formatWorkoutVolume(workout) {
    if (workout.workoutSets && workout.workoutReps) {
        return `${workout.workoutSets} x ${workout.workoutReps}`;
    }

    return "--";
}

function deriveMetrics(user) {
    const height = toNumber(user.height);
    const weight = toNumber(user.weight);
    const age = user.age ?? calculateAge(user.birthdate);
    const bmi = height > 0 && weight > 0 ? weight / (height * height) : null;
    const canEstimateCalories = ["male", "female"].includes(user.sex) && age !== null && height > 0 && weight > 0;
    const activityFactor = ACTIVITY_FACTORS[user.activity] || ACTIVITY_FACTORS.sedentary;
    let bmr = null;
    let tdee = null;

    if (canEstimateCalories) {
        const sexAdjustment = user.sex === "male" ? 5 : -161;
        bmr = (10 * weight) + (6.25 * height * 100) - (5 * age) + sexAdjustment;
        tdee = bmr * activityFactor;
    }

    return {
        bmi: roundValue(bmi, 2),
        bmiCategory: classifyBmi(bmi),
        bmiPercent: bmiPercent(bmi),
        bmiStatus: bmiStatus(bmi),
        bmr: roundValue(bmr),
        tdee: roundValue(tdee),
        maintenanceCalories: roundValue(tdee),
        mildDeficitCalories: roundValue(tdee === null ? null : Math.max(tdee - 250, 0)),
        weightLossCalories: roundValue(tdee === null ? null : Math.max(tdee - 500, 0)),
        proteinGrams: roundValue(weight > 0 ? weight * 1.6 : null),
        canEstimateCalories,
    };
}

function calculateAge(birthdateValue) {
    if (!birthdateValue) {
        return null;
    }

    const birthdate = new Date(birthdateValue);

    if (Number.isNaN(birthdate.getTime())) {
        return null;
    }

    const today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();

    if (
        today.getMonth() < birthdate.getMonth() ||
        (today.getMonth() === birthdate.getMonth() && today.getDate() < birthdate.getDate())
    ) {
        age -= 1;
    }

    return age;
}

function classifyBmi(bmi) {
    if (bmi === null || Number.isNaN(bmi)) return "Not available";
    if (bmi < 18.5) return "Underweight";
    if (bmi < 25) return "Normal weight";
    if (bmi < 30) return "Overweight";

    return "Obese";
}

function bmiStatus(bmi) {
    switch (classifyBmi(bmi)) {
        case "Underweight":
            return "Below the standard BMI range. Build toward steady nutrition, training, and recovery.";
        case "Normal weight":
            return "Within the standard BMI range. Keep training, nutrition, and recovery consistent.";
        case "Overweight":
            return "Above the standard BMI range. Use it as one signal while tracking strength, habits, and recovery.";
        case "Obese":
            return "High BMI range. Treat it as a screening signal and pair it with sustainable health markers.";
        default:
            return "Add height and weight to calculate BMI.";
    }
}

function bmiPercent(bmi) {
    if (bmi === null || Number.isNaN(bmi)) {
        return 0;
    }

    const minimum = 14;
    const maximum = 40;

    return Math.max(0, Math.min(100, ((bmi - minimum) / (maximum - minimum)) * 100));
}

function closeSettingsForm(form) {
    form.hidden = true;
    clearFormFeedback(form);
    const toggle = document.querySelector(`[data-settings-toggle="${form.id}"]`);
    toggle?.setAttribute("aria-expanded", "false");
}

function renderFormErrors(form, errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const error = form.querySelector(`[data-field-error="${field}"]`);

        if (error) {
            error.textContent = Array.isArray(messages) ? messages[0] : messages;
        }
    });
}

function clearFormFeedback(form) {
    form.querySelectorAll("[data-field-error]").forEach((element) => {
        element.textContent = "";
    });
    setFormStatus(form, "");
}

function setFormStatus(form, message, isError = false) {
    const status = form.querySelector("[data-form-status]");

    if (!status) {
        return;
    }

    status.textContent = message;
    status.classList.toggle("is-error", isError);
}

function notify(type, message) {
    const toaster = window.GreekGodsToast;

    if (message && typeof toaster?.[type] === "function") {
        toaster[type](message);
    }
}

async function confirmAction(message, options = {}) {
    if (typeof window.GreekGodsConfirm === "function") {
        return window.GreekGodsConfirm(message, options);
    }

    notify("warning", "Confirmation is not ready yet. Please try again.");
    return false;
}

function setFormLoading(form, isLoading) {
    form.querySelectorAll("input, select, button").forEach((element) => {
        element.disabled = isLoading;
    });

    const submitButton = form.querySelector('button[type="submit"]');

    if (!submitButton) {
        return;
    }

    if (isLoading) {
        submitButton.dataset.originalText = submitButton.textContent;
        submitButton.textContent = "Saving...";
        return;
    }

    if (submitButton.dataset.originalText) {
        submitButton.textContent = submitButton.dataset.originalText;
        delete submitButton.dataset.originalText;
    }
}

function formatMeasurement(value, precision, unit) {
    const number = toNumber(value);

    if (number === null || Number.isNaN(number)) {
        return "--";
    }

    return `${number.toFixed(precision)} ${unit}`;
}

function formatNumber(value, precision = 0) {
    const number = toNumber(value);

    if (number === null || Number.isNaN(number)) {
        return "--";
    }

    return number.toLocaleString("en-US", {
        maximumFractionDigits: precision,
        minimumFractionDigits: precision,
    });
}

function formatCalories(value) {
    const number = toNumber(value);

    if (number === null || Number.isNaN(number)) {
        return "--";
    }

    return `${Math.round(number).toLocaleString("en-US")} kcal/day`;
}

function formatProtein(value) {
    const number = toNumber(value);

    if (number === null || Number.isNaN(number)) {
        return "--";
    }

    return `${Math.round(number).toLocaleString("en-US")} g/day`;
}

function setText(id, value) {
    const element = document.getElementById(id);

    if (element) {
        element.textContent = value;
    }
}

function setValue(id, value) {
    const element = document.getElementById(id);

    if (element) {
        element.value = value;
    }
}

function toggleHidden(id, shouldHide) {
    const element = document.getElementById(id);

    if (element) {
        element.classList.toggle("is-hidden", shouldHide);
    }
}

function toNumber(value) {
    if (value === null || value === undefined || value === "") {
        return null;
    }

    const number = Number.parseFloat(value);

    return Number.isNaN(number) ? null : number;
}

function roundValue(value, precision = 0) {
    if (value === null || Number.isNaN(value)) {
        return null;
    }

    const multiplier = 10 ** precision;

    return Math.round(value * multiplier) / multiplier;
}
