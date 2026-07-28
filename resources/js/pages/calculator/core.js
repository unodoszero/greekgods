export const ACTIVITY_FACTORS = Object.freeze({
    sedentary: 1.2,
    light: 1.375,
    moderate: 1.55,
    active: 1.725,
    very_active: 1.9,
});

export const PROTEIN_FACTORS = Object.freeze({
    sedentary: 0.8,
    light: 1.2,
    moderate: 1.4,
    active: 1.6,
    very_active: 2.2,
});

export function convertHeightToMetres(value, unit) {
    const height = Number(value);

    if (!Number.isFinite(height) || height <= 0) {
        return Number.NaN;
    }

    const conversions = {
        cm: height / 100,
        in: height * 0.0254,
        m: height,
        ft: height * 0.3048,
    };

    return conversions[unit] ?? Number.NaN;
}

export function convertWeightToKilograms(value, unit) {
    const weight = Number(value);

    if (!Number.isFinite(weight) || weight <= 0) {
        return Number.NaN;
    }

    const conversions = {
        kg: weight,
        lb: weight * 0.453592,
    };

    return conversions[unit] ?? Number.NaN;
}

export function calculateBmi(heightMetres, weightKilograms) {
    if (!Number.isFinite(heightMetres) || heightMetres <= 0
        || !Number.isFinite(weightKilograms) || weightKilograms <= 0) {
        return Number.NaN;
    }

    return weightKilograms / (heightMetres ** 2);
}

export function classifyBmi(bmi) {
    if (!Number.isFinite(bmi) || bmi < 0) {
        return "";
    }

    if (bmi < 18.5) {
        return "Underweight";
    }

    if (bmi < 25) {
        return "Healthy range";
    }

    if (bmi < 30) {
        return "Overweight";
    }

    return "Obese range";
}

export function calculateBmr({ formula, heightMetres, weightKilograms, age, sex, bodyFatPercentage }) {
    const numericAge = Number(age);

    if (!Number.isFinite(heightMetres) || heightMetres <= 0
        || !Number.isFinite(weightKilograms) || weightKilograms <= 0
        || !Number.isFinite(numericAge) || numericAge <= 0) {
        return Number.NaN;
    }

    if (formula === "katch_mcardle") {
        const bodyFat = Number(bodyFatPercentage);

        if (!Number.isFinite(bodyFat) || bodyFat < 1 || bodyFat > 70) {
            return Number.NaN;
        }

        const leanBodyMass = weightKilograms * (1 - bodyFat / 100);
        return 370 + (21.6 * leanBodyMass);
    }

    if (!["male", "female"].includes(sex)) {
        return Number.NaN;
    }

    const heightCentimetres = heightMetres * 100;

    if (formula === "mifflin_st_jeor") {
        const sexConstant = sex === "male" ? 5 : -161;
        return (10 * weightKilograms) + (6.25 * heightCentimetres) - (5 * numericAge) + sexConstant;
    }

    if (formula === "revised_harris_benedict") {
        return sex === "male"
            ? 88.362 + (13.397 * weightKilograms) + (4.799 * heightCentimetres) - (5.677 * numericAge)
            : 447.593 + (9.247 * weightKilograms) + (3.098 * heightCentimetres) - (4.330 * numericAge);
    }

    return Number.NaN;
}

export function calculateTdee(bmr, activity) {
    const factor = ACTIVITY_FACTORS[activity];
    return Number.isFinite(bmr) && factor ? bmr * factor : Number.NaN;
}

export function calculateProtein(weightKilograms, activity) {
    const factor = PROTEIN_FACTORS[activity];
    return Number.isFinite(weightKilograms) && weightKilograms > 0 && factor
        ? weightKilograms * factor
        : Number.NaN;
}

export function calculateCalorieTargets(tdee, minimumCalories = 1000) {
    if (!Number.isFinite(tdee) || tdee <= 0) {
        return [];
    }

    return [0, 500, 750, 1000].map((deficit) => {
        const calories = tdee - deficit;

        return {
            deficit,
            calories,
            blocked: deficit > 0 && calories < minimumCalories,
        };
    });
}
