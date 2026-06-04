const DAYS = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

const workoutsList = [
    "Bench Press", "Squat", "Deadlift", "Pull-Up", "Push-Up", "Overhead Press",
    "Dumbbell Row", "Barbell Row", "Chin-Up", "Dips", "Bicep Curl", "Tricep Pushdown",
    "Lateral Raise", "Face Pull", "Plank", "Russian Twist", "Leg Raise", "Lunges",
    "Bulgarian Split Squat", "Step-Up", "Calf Raise", "Glute Bridge", "Hip Thrust",
    "Kettlebell Swing", "Farmer's Walk", "Dumbbell Fly", "Incline Bench Press",
    "Arnold Press", "Shrugs", "Barbell Curl", "Hammer Curl", "Skull Crusher",
    "Close-Grip Bench Press", "Cable Fly", "Hanging Leg Raise", "Dead Bug",
    "Romanian Deadlift", "Sumo Deadlift", "Leg Press (Machine)", "Lat Pulldown",
    "Seated Row (Machine)", "Chest-Supported Row", "T-Bar Row", "Trap Bar Deadlift",
    "Goblet Squat", "Reverse Lunge", "Medicine Ball Twist", "Cable Woodchopper",
    "Clean Pull", "High Pull", "Pendlay Row", "Snatch Grip Deadlift", "Front Squat",
    "Hack Squat (Machine)", "Seated Leg Extension", "Seated Chest Press", "Upright Row",
    "Smith Machine Incline Bench Press", "Pec Deck", "High to Low Cable Fly",
    "Straight Bar Tricep Pushdown", "Overhead Cable Triceps Extension",
    "Chest Supported Row", "Cable Seated Row", "Reverse Pec Deck", "Lat Prayer",
    "Hip Abduction", "Hamstring Curl", "Supported Leg Raise",
];

const catalog = typeof splitCatalog !== "undefined" ? splitCatalog : {};
const initialProgramState = typeof programState !== "undefined" ? programState : { program: null, workouts: [] };

let selectedSplit = initialProgramState.program?.split || "";
let selectedSchedule = initialProgramState.program?.schedule || "";
let isProgramSaved = Boolean(initialProgramState.program);
let workouts = Array.isArray(initialProgramState.workouts) ? [...initialProgramState.workouts] : [];
let activeWorkoutId = null;
let isRequestPending = false;

document.addEventListener("DOMContentLoaded", () => {
    renderSplitCards();
    renderScheduleOptions();
    renderSchedulePreview();
    renderBoard();
    bindProgramActions();
    bindWorkoutForm();
    bindAutocomplete();
});

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || "";
}

function currentScheduleDefinition() {
    return catalog[selectedSplit]?.schedules?.[selectedSchedule] || null;
}

function currentDayLabel(day) {
    return currentScheduleDefinition()?.days?.[day] || "Choose a schedule";
}

function isTrainingDay(day) {
    const label = currentDayLabel(day);
    return isProgramSaved && label && label !== "Rest" && label !== "Choose a schedule";
}

function renderSplitCards() {
    const container = document.getElementById("split-options");
    if (!container) {
        return;
    }

    container.innerHTML = "";

    Object.entries(catalog).forEach(([id, split]) => {
        const button = document.createElement("button");
        button.type = "button";
        button.className = "split-card";
        button.dataset.split = id;
        button.setAttribute("aria-pressed", String(selectedSplit === id));
        button.disabled = isProgramSaved || isRequestPending;

        const scheduleCount = Object.keys(split.schedules || {}).length;
        button.innerHTML = `
            <span>${split.shortLabel || split.label}</span>
            <strong>${split.label}</strong>
            <small>${split.description || ""}</small>
            <em>${scheduleCount} schedule${scheduleCount === 1 ? "" : "s"}</em>
        `;

        button.addEventListener("click", () => {
            selectedSplit = id;
            selectedSchedule = "";
            setStatus("");
            renderSplitCards();
            renderScheduleOptions();
            renderSchedulePreview();
            renderBoard();
        });

        container.appendChild(button);
    });
}

function renderScheduleOptions() {
    const select = document.getElementById("schedule-options");
    if (!select) {
        return;
    }

    select.innerHTML = "";

    if (!selectedSplit || !catalog[selectedSplit]) {
        select.disabled = true;
        select.appendChild(option("", "Select a split first"));
        return;
    }

    select.disabled = isProgramSaved || isRequestPending;
    select.appendChild(option("", "Select schedule"));

    Object.entries(catalog[selectedSplit].schedules || {}).forEach(([id, schedule]) => {
        const scheduleOption = option(id, schedule.label);
        scheduleOption.selected = selectedSchedule === id;
        select.appendChild(scheduleOption);
    });
}

function option(value, label) {
    const item = document.createElement("option");
    item.value = value;
    item.textContent = label;
    return item;
}

function renderSchedulePreview() {
    const preview = document.getElementById("schedule-preview");
    if (!preview) {
        return;
    }

    preview.innerHTML = "";

    const schedule = currentScheduleDefinition();

    DAYS.forEach((day) => {
        const label = schedule?.days?.[day] || "Pending";
        const previewDay = document.createElement("div");
        previewDay.className = `preview-day ${label === "Rest" ? "is-rest" : ""}`;
        previewDay.innerHTML = `
            <span>${day.slice(0, 3)}</span>
            <strong>${label}</strong>
        `;
        preview.appendChild(previewDay);
    });
}

function renderBoard() {
    DAYS.forEach((day) => {
        const card = document.querySelector(`[data-day="${day}"]`);
        if (!card) {
            return;
        }

        const label = currentDayLabel(day);
        const dayWorkouts = workouts.filter((workout) => workout.workoutDay === day);
        const isRest = label === "Rest";
        const isReady = isTrainingDay(day);

        card.classList.toggle("is-rest", isRest);
        card.classList.toggle("is-locked", !isReady);
        card.querySelector(".split-name").textContent = label;
        card.querySelector(".workout-count").textContent = String(dayWorkouts.length);

        const empty = card.querySelector(".day-empty");
        if (!isProgramSaved) {
            empty.textContent = "Save a split to start adding workouts.";
        } else if (isRest) {
            empty.textContent = "Rest day.";
        } else if (dayWorkouts.length === 0) {
            empty.textContent = "Add your first workout to this day.";
        } else {
            empty.textContent = "";
        }

        const addButton = card.querySelector(".add");
        addButton.disabled = !isReady || isRequestPending;
        addButton.onclick = () => openWorkoutForm(day);

        renderWorkoutList(card.querySelector(".workouts"), dayWorkouts);
    });

    document.getElementById("board-empty-state").textContent = isProgramSaved
        ? "Training days are unlocked. Rest days stay disabled."
        : "Save a split to start adding workouts.";

    document.getElementById("save-program").disabled = isRequestPending || isProgramSaved || !selectedSplit || !selectedSchedule;
    document.getElementById("change-program").disabled = isRequestPending || !isProgramSaved;
}

function renderWorkoutList(container, dayWorkouts) {
    container.innerHTML = "";

    dayWorkouts.forEach((workout) => {
        const item = document.createElement("div");
        item.className = "workout-row";

        const details = document.createElement("div");
        details.className = "workout-details";

        const name = document.createElement("strong");
        name.textContent = workout.workoutName;
        const meta = document.createElement("span");
        meta.textContent = formatWorkoutMeta(workout);

        details.append(name, meta);

        const actions = document.createElement("div");
        actions.className = "workout-actions";

        const edit = document.createElement("button");
        edit.type = "button";
        edit.textContent = "Edit";
        edit.disabled = isRequestPending;
        edit.addEventListener("click", () => openWorkoutForm(workout.workoutDay, workout));

        const remove = document.createElement("button");
        remove.type = "button";
        remove.textContent = "Delete";
        remove.disabled = isRequestPending;
        remove.addEventListener("click", () => deleteWorkout(workout.id));

        actions.append(edit, remove);
        item.append(details, actions);
        container.appendChild(item);
    });
}

function bindProgramActions() {
    document.getElementById("schedule-options")?.addEventListener("change", (event) => {
        selectedSchedule = event.target.value;
        setStatus("");
        renderSchedulePreview();
        renderBoard();
    });

    document.getElementById("save-program")?.addEventListener("click", saveProgram);
    document.getElementById("change-program")?.addEventListener("click", resetProgram);
}

async function saveProgram() {
    if (isRequestPending) {
        return;
    }

    if (!selectedSplit || !selectedSchedule) {
        setStatus("Select a split and schedule first.", true);
        notify("warning", "Select a split and schedule first.");
        return;
    }

    setPending(true);
    const result = await requestJson("/program", "POST", {
        split: selectedSplit,
        schedule: selectedSchedule,
    });

    if (!result.success) {
        setPending(false);
        setStatus(result.message || "Unable to save program.", true);
        notify("error", result.message || "Unable to save program.");
        return;
    }

    isProgramSaved = true;
    workouts = Array.isArray(result.workouts) ? result.workouts : [];
    setStatus(result.message || "Program saved.");
    notify("success", result.message || "Program saved.");
    setPending(false);
    renderSplitCards();
    renderScheduleOptions();
    renderBoard();
}

async function resetProgram() {
    if (isRequestPending) {
        return;
    }

    const confirmed = await confirmAction("Changing program will delete the workouts in your current program. Continue?", {
        title: "Change program",
        confirmLabel: "Change program",
    });

    if (!confirmed) {
        return;
    }

    const previousState = {
        selectedSplit,
        selectedSchedule,
        isProgramSaved,
        workouts: [...workouts],
    };

    isRequestPending = true;
    selectedSplit = "";
    selectedSchedule = "";
    isProgramSaved = false;
    workouts = [];
    closeWorkoutForm();
    setStatus("Program reset. Saving changes...");
    renderSplitCards();
    renderScheduleOptions();
    renderSchedulePreview();
    renderBoard();

    const result = await requestJson("/program", "DELETE");

    if (!result.success) {
        selectedSplit = previousState.selectedSplit;
        selectedSchedule = previousState.selectedSchedule;
        isProgramSaved = previousState.isProgramSaved;
        workouts = previousState.workouts;
        isRequestPending = false;
        setStatus(result.message || "Unable to reset program.", true);
        notify("error", result.message || "Unable to reset program.");
        renderSplitCards();
        renderScheduleOptions();
        renderSchedulePreview();
        renderBoard();
        return;
    }

    isRequestPending = false;
    setStatus("Program reset. Choose a new split.");
    notify("info", result.message || "Program reset.");
    renderSplitCards();
    renderScheduleOptions();
    renderSchedulePreview();
    renderBoard();
}

function bindWorkoutForm() {
    document.getElementById("workout-form")?.addEventListener("submit", saveWorkout);
    document.getElementById("workout-form-cancel")?.addEventListener("click", closeWorkoutForm);
    document.getElementById("workout-modal")?.addEventListener("click", (event) => {
        if (event.target.id === "workout-modal") {
            closeWorkoutForm();
        }
    });
}

function formatWorkoutMeta(workout) {
    if (workout.workoutSets && workout.workoutReps) {
        return `${workout.workoutSets} sets x ${workout.workoutReps} reps`;
    }

    return workout.workoutFocus || "No details set.";
}

function openWorkoutForm(day, workout = null) {
    if (isRequestPending || !isTrainingDay(day)) {
        return;
    }

    activeWorkoutId = workout?.id || null;
    document.getElementById("workout-form-title").textContent = workout ? "Edit workout" : "Add workout";
    document.getElementById("workout-form-day").textContent = day;
    document.getElementById("workout-id").value = workout?.id || "";
    document.getElementById("workout-day").value = day;
    document.getElementById("workout-name").value = workout?.workoutName || "";
    document.getElementById("workout-sets").value = workout?.workoutSets || "";
    document.getElementById("workout-reps").value = workout?.workoutReps || "";
    document.querySelector(".autocomplete-suggestions").innerHTML = "";
    document.getElementById("workout-modal").hidden = false;
    document.getElementById("workout-name").focus();
}

function closeWorkoutForm() {
    activeWorkoutId = null;
    document.getElementById("workout-modal").hidden = true;
    document.getElementById("workout-form").reset();
    document.querySelector(".autocomplete-suggestions").innerHTML = "";
}

async function saveWorkout(event) {
    event.preventDefault();

    if (isRequestPending) {
        return;
    }

    const payload = {
        day: document.getElementById("workout-day").value,
        workout_name: document.getElementById("workout-name").value.trim(),
        workout_sets: Number(document.getElementById("workout-sets").value),
        workout_reps: Number(document.getElementById("workout-reps").value),
    };

    if (!payload.workout_name || !payload.workout_sets || !payload.workout_reps) {
        setStatus("Fill in workout name, sets, and reps.", true);
        notify("warning", "Fill in workout name, sets, and reps.");
        return;
    }

    const url = activeWorkoutId ? `/program/workouts/${activeWorkoutId}` : "/program/workouts";
    const method = activeWorkoutId ? "PATCH" : "POST";
    setPending(true);
    const result = await requestJson(url, method, payload);

    if (!result.success) {
        setPending(false);
        setStatus(result.message || "Unable to save workout.", true);
        notify("error", result.message || "Unable to save workout.");
        return;
    }

    if (activeWorkoutId) {
        workouts = workouts.map((workout) => workout.id === result.workout.id ? result.workout : workout);
    } else {
        workouts.push(result.workout);
    }

    closeWorkoutForm();
    setStatus(result.message || "Workout saved.");
    notify("success", result.message || "Workout saved.");
    setPending(false);
    renderBoard();
}

async function deleteWorkout(id) {
    if (isRequestPending) {
        return;
    }

    const confirmed = await confirmAction("Delete this workout?", {
        title: "Delete workout",
        confirmLabel: "Delete",
    });

    if (!confirmed) {
        return;
    }

    const previousWorkouts = [...workouts];
    workouts = workouts.filter((workout) => workout.id !== id);
    setPending(true);
    setStatus("Workout deleted. Saving changes...");

    const result = await requestJson(`/program/workouts/${id}`, "DELETE");

    if (!result.success) {
        workouts = previousWorkouts;
        setPending(false);
        setStatus(result.message || "Unable to delete workout.", true);
        notify("error", result.message || "Unable to delete workout.");
        renderBoard();
        return;
    }

    setStatus(result.message || "Workout deleted.");
    notify("info", result.message || "Workout deleted.");
    setPending(false);
    renderBoard();
}

function setPending(isPending) {
    isRequestPending = isPending;
    const formSaveButton = document.getElementById("form-add");
    const formCancelButton = document.getElementById("workout-form-cancel");

    if (formSaveButton) {
        formSaveButton.disabled = isPending;
    }

    if (formCancelButton) {
        formCancelButton.disabled = isPending;
    }

    renderSplitCards();
    renderScheduleOptions();
    renderBoard();
}

async function requestJson(url, method, payload = null) {
    try {
        const response = await fetch(url, {
            method,
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken(),
            },
            body: payload ? JSON.stringify(payload) : null,
        });
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            return {
                success: false,
                message: data.message || Object.values(data.errors || {})[0]?.[0] || "Request failed.",
            };
        }

        return data;
    } catch (error) {
        return {
            success: false,
            message: "Network request failed.",
        };
    }
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

function setStatus(message, isError = false) {
    const status = document.getElementById("program-status");
    if (!status) {
        return;
    }

    status.textContent = message;
    status.classList.toggle("is-error", isError);
}

function bindAutocomplete() {
    const input = document.getElementById("workout-name");
    const suggestions = document.querySelector(".autocomplete-suggestions");

    input?.addEventListener("input", () => {
        const value = input.value.trim().toLowerCase();
        suggestions.innerHTML = "";

        if (!value) {
            return;
        }

        workoutsList
            .filter((workout) => workout.toLowerCase().startsWith(value))
            .slice(0, 8)
            .forEach((workout) => {
                const item = document.createElement("button");
                item.type = "button";
                item.textContent = workout;
                item.addEventListener("click", () => {
                    input.value = workout;
                    suggestions.innerHTML = "";
                });
                suggestions.appendChild(item);
            });
    });

    input?.addEventListener("keydown", (event) => {
        if (event.key === "Tab" && suggestions.firstElementChild) {
            event.preventDefault();
            input.value = suggestions.firstElementChild.textContent;
            suggestions.innerHTML = "";
        }
    });
}
