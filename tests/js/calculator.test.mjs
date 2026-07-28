import test from "node:test";
import assert from "node:assert/strict";

import {
    calculateBmi,
    calculateBmr,
    calculateCalorieTargets,
    calculateProtein,
    calculateTdee,
    classifyBmi,
    convertHeightToMetres,
    convertWeightToKilograms,
} from "../../resources/js/pages/calculator/core.js";

test("converts supported height and weight units", () => {
    assert.equal(convertHeightToMetres(180, "cm"), 1.8);
    assert.equal(convertHeightToMetres(1.8, "m"), 1.8);
    assert.equal(convertHeightToMetres(70, "in").toFixed(3), "1.778");
    assert.equal(convertHeightToMetres(6, "ft").toFixed(4), "1.8288");
    assert.equal(convertWeightToKilograms(75, "kg"), 75);
    assert.equal(convertWeightToKilograms(165, "lb").toFixed(2), "74.84");
});

test("calculates continuous BMI category boundaries", () => {
    assert.equal(calculateBmi(1.8, 81).toFixed(1), "25.0");
    assert.equal(classifyBmi(18.49), "Underweight");
    assert.equal(classifyBmi(18.5), "Healthy range");
    assert.equal(classifyBmi(24.99), "Healthy range");
    assert.equal(classifyBmi(25), "Overweight");
    assert.equal(classifyBmi(29.99), "Overweight");
    assert.equal(classifyBmi(30), "Obese range");
});

test("calculates all supported BMR formulas", () => {
    const base = {
        heightMetres: 1.75,
        weightKilograms: 75,
        age: 30,
        sex: "male",
        bodyFatPercentage: 20,
    };

    assert.equal(calculateBmr({ ...base, formula: "mifflin_st_jeor" }).toFixed(2), "1698.75");
    assert.equal(calculateBmr({ ...base, formula: "revised_harris_benedict" }).toFixed(2), "1762.65");
    assert.equal(calculateBmr({ ...base, formula: "katch_mcardle" }).toFixed(2), "1666.00");
});

test("applies activity and protein factors", () => {
    assert.equal(calculateTdee(1600, "moderate"), 2480);
    assert.equal(calculateProtein(75, "active"), 120);
});

test("blocks only deficit targets below the calculator safety floor", () => {
    const targets = calculateCalorieTargets(1700);

    assert.deepEqual(targets.map(({ deficit, blocked }) => ({ deficit, blocked })), [
        { deficit: 0, blocked: false },
        { deficit: 500, blocked: false },
        { deficit: 750, blocked: true },
        { deficit: 1000, blocked: true },
    ]);
    assert.equal(targets[2].calories, 950);
});
