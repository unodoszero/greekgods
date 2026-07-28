import {
    calculateBmi,
    calculateBmr,
    calculateCalorieTargets,
    calculateProtein,
    calculateTdee,
    classifyBmi,
    convertHeightToMetres,
    convertWeightToKilograms,
} from "./core.js";

const form = document.getElementById("calculator-form");
const results = document.getElementById("calculator-results");
const resultsHeading = document.getElementById("results-heading");
const formulaSelect = document.getElementById("formula");
const bodyFatContainer = document.getElementById("katchMcardleContainer");
const bodyFatRange = document.getElementById("bodyFat");
const bodyFatNumber = document.getElementById("sliderValue");
const bodyFatOutput = document.getElementById("bodyFatOutput");

const activityLabels = Object.freeze({
    sedentary: "Sedentary",
    light: "Light",
    moderate: "Moderate",
    active: "Active",
    very_active: "Very active",
});

const formulaDescriptions = Object.freeze({
    mifflin_st_jeor: "Uses age, sex, height, and weight to estimate resting energy needs.",
    revised_harris_benedict: "An alternative estimate based on age, sex, height, and weight.",
    katch_mcardle: "Uses weight and your body-fat estimate to calculate lean-body-mass needs.",
});

function element(id) {
    return document.getElementById(id);
}

function notify(type, message) {
    const toaster = window.GreekGodsToast;

    if (message && typeof toaster?.[type] === "function") {
        toaster[type](message);
    }
}

function setFieldError(fieldName, message) {
    const error = element(`${fieldName}-error`);
    const field = fieldName === "gender"
        ? document.querySelector(".segmented-control")
        : fieldName === "body-fat"
            ? bodyFatNumber
            : element(fieldName);

    if (error) {
        error.textContent = message;
    }

    if (fieldName === "gender") {
        document.querySelectorAll('input[name="gender"]').forEach((input) => {
            input.setAttribute("aria-invalid", message ? "true" : "false");
        });
        field?.classList.toggle("has-error", Boolean(message));
        return;
    }

    field?.setAttribute("aria-invalid", message ? "true" : "false");
}

function clearFieldError(fieldName) {
    setFieldError(fieldName, "");
}

function formValues() {
    return {
        age: Number(element("age").value),
        sex: document.querySelector('input[name="gender"]:checked')?.value ?? "",
        height: Number(element("height").value),
        heightUnit: element("heightMetric").value,
        weight: Number(element("weight").value),
        weightUnit: element("weightMetric").value,
        activity: element("activity").value,
        formula: formulaSelect.value,
        bodyFatPercentage: Number(bodyFatNumber.value),
    };
}

function validate(values) {
    const errors = {};

    if (!Number.isInteger(values.age) || values.age < 18 || values.age > 100) {
        errors.age = "Enter an age from 18 to 100.";
    }

    if (!["male", "female"].includes(values.sex)) {
        errors.gender = "Choose the sex used by the formula.";
    }

    if (!Number.isFinite(values.height) || values.height <= 0) {
        errors.height = "Enter a height greater than zero.";
    }

    if (!Number.isFinite(values.weight) || values.weight <= 0) {
        errors.weight = "Enter a weight greater than zero.";
    }

    if (!Object.hasOwn(activityLabels, values.activity)) {
        errors.activity = "Choose your typical activity level.";
    }

    if (!["mifflin_st_jeor", "revised_harris_benedict", "katch_mcardle"].includes(values.formula)) {
        errors.formula = "Choose a supported BMR formula.";
    }

    if (values.formula === "katch_mcardle"
        && (!Number.isFinite(values.bodyFatPercentage)
            || values.bodyFatPercentage < 1
            || values.bodyFatPercentage > 70)) {
        errors["body-fat"] = "Enter a body-fat estimate from 1% to 70%.";
    }

    ["age", "gender", "height", "weight", "activity", "formula", "body-fat"].forEach((field) => {
        setFieldError(field, errors[field] ?? "");
    });

    return errors;
}

function formatNumber(value, maximumFractionDigits = 0) {
    return new Intl.NumberFormat(undefined, {
        maximumFractionDigits,
        minimumFractionDigits: maximumFractionDigits,
    }).format(value);
}

function formatCalories(value) {
    return `${formatNumber(value)} kcal/day`;
}

function formatProtein(value) {
    return `${formatNumber(value)} g/day`;
}

function renderTarget(elementId, target) {
    const output = element(elementId);
    output.classList.toggle("is-blocked", target.blocked);
    output.textContent = target.blocked
        ? "Below safe calculator range"
        : formatCalories(target.calories);
}

function renderResults(values, metrics) {
    element("age-result").textContent = `${values.age} years`;
    element("gender-result").textContent = values.sex === "male" ? "Male" : "Female";
    element("height-result").textContent = `${formatNumber(values.height, values.height % 1 ? 1 : 0)} ${values.heightUnit}`;
    element("weight-result").textContent = `${formatNumber(values.weight, values.weight % 1 ? 1 : 0)} ${values.weightUnit}`;
    element("activity-result").textContent = activityLabels[values.activity];

    element("bmi-result").textContent = formatNumber(metrics.bmi, 1);
    element("classification-result").textContent = classifyBmi(metrics.bmi);
    element("bmr").textContent = formatCalories(metrics.bmr);
    element("tdee-result").textContent = formatCalories(metrics.tdee);
    element("protein-intake").textContent = formatProtein(metrics.protein);

    const targetIds = ["maintain-weight", "mid-weight-loss", "weight-loss", "extreme-weight-loss"];
    metrics.targets.forEach((target, index) => renderTarget(targetIds[index], target));
    element("target-warning").hidden = !metrics.targets.some((target) => target.blocked);

    results.hidden = false;
    requestAnimationFrame(() => {
        results.scrollIntoView({ behavior: "smooth", block: "start" });
        resultsHeading.focus({ preventScroll: true });
    });
}

function calculate(values) {
    const heightMetres = convertHeightToMetres(values.height, values.heightUnit);
    const weightKilograms = convertWeightToKilograms(values.weight, values.weightUnit);
    const bmi = calculateBmi(heightMetres, weightKilograms);
    const bmr = calculateBmr({
        formula: values.formula,
        heightMetres,
        weightKilograms,
        age: values.age,
        sex: values.sex,
        bodyFatPercentage: values.bodyFatPercentage,
    });
    const tdee = calculateTdee(bmr, values.activity);
    const protein = calculateProtein(weightKilograms, values.activity);

    return {
        bmi,
        bmr,
        tdee,
        protein,
        targets: calculateCalorieTargets(tdee),
    };
}

function syncBodyFat(source, target) {
    const numericValue = Math.min(70, Math.max(1, Number(source.value) || 1));
    target.value = String(numericValue);
    bodyFatOutput.value = String(numericValue);
    clearFieldError("body-fat");
}

function updateFormulaFields() {
    const usesBodyFat = formulaSelect.value === "katch_mcardle";
    bodyFatContainer.hidden = !usesBodyFat;
    bodyFatRange.disabled = !usesBodyFat;
    bodyFatNumber.disabled = !usesBodyFat;
    element("formula-help").textContent = formulaDescriptions[formulaSelect.value] ?? "";

    if (!usesBodyFat) {
        clearFieldError("body-fat");
    }
}

form.addEventListener("submit", (event) => {
    event.preventDefault();

    const values = formValues();
    const errors = validate(values);

    if (Object.keys(errors).length > 0) {
        const firstError = Object.keys(errors)[0];
        const firstField = firstError === "gender"
            ? document.querySelector('input[name="gender"]')
            : firstError === "body-fat"
                ? bodyFatNumber
                : element(firstError);

        firstField?.focus();
        notify("warning", "Please review the highlighted calculator fields.");
        return;
    }

    const metrics = calculate(values);

    if ([metrics.bmi, metrics.bmr, metrics.tdee, metrics.protein].some((value) => !Number.isFinite(value))) {
        notify("error", "We could not calculate these estimates. Please review your inputs.");
        return;
    }

    renderResults(values, metrics);
    notify("success", "Your fitness estimates are ready.");
});

form.addEventListener("input", (event) => {
    if (event.target instanceof HTMLInputElement || event.target instanceof HTMLSelectElement) {
        const errorName = event.target.name === "gender" ? "gender" : event.target.id;

        if (["age", "gender", "height", "weight", "activity", "formula"].includes(errorName)) {
            clearFieldError(errorName);
        }
    }
});

formulaSelect.addEventListener("change", updateFormulaFields);
bodyFatRange.addEventListener("input", () => syncBodyFat(bodyFatRange, bodyFatNumber));
bodyFatNumber.addEventListener("input", () => syncBodyFat(bodyFatNumber, bodyFatRange));

element("edit-details").addEventListener("click", () => {
    form.scrollIntoView({ behavior: "smooth", block: "start" });
    element("age").focus({ preventScroll: true });
});

updateFormulaFields();
