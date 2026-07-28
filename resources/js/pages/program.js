const DAYS = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
const catalog = typeof splitCatalog === "object" ? splitCatalog : {};
const templates = typeof workoutTemplates === "object" ? workoutTemplates : {};
const initialState = typeof programState === "object" ? programState : { program: null, workouts: [] };
const workoutsList = [...new Set(Object.values(templates)
    .flatMap((split) => Object.values(split))
    .flatMap((workouts) => workouts.map((workout) => workout.name)))].sort();

let page = initialState.program ? "summary" : "selection";
let selectedSplit = initialState.program?.split || "";
let selectedSchedule = initialState.program?.schedule || "";
let savedProgram = initialState.program || null;
let savedWorkouts = Array.isArray(initialState.workouts) ? initialState.workouts.map(normalizeSavedWorkout) : [];
let draftWorkouts = [];
let draftGenerated = false;
let modalMode = "draft";
let activeWorkoutKey = null;
let isRequestPending = false;
let localId = 0;

document.addEventListener("DOMContentLoaded", () => {
    bindActions();
    bindWorkoutForm();
    bindAutocomplete();
    bindCarousels();
    render();
});

function normalizeSavedWorkout(workout) {
    return {
        ...workout,
        workoutSets: Number(workout.workoutSets),
        repsMin: Number(workout.repsMin),
        repsMax: Number(workout.repsMax),
        position: Number(workout.position),
    };
}

function localKey() {
    localId += 1;
    return `draft-${localId}`;
}

function currentSchedule() {
    return catalog[selectedSplit]?.schedules?.[selectedSchedule] || null;
}

function dayLabel(day) {
    return currentSchedule()?.days?.[day] || "Pending";
}

function isTrainingDay(day) {
    const label = dayLabel(day);
    return label !== "Rest" && label !== "Pending";
}

function render() {
    const isSummary = page === "summary";
    document.querySelectorAll("[data-builder-chrome]").forEach((element) => {
        element.hidden = isSummary;
    });
    document.querySelector(".program-main")?.classList.toggle("is-summary", isSummary);

    document.querySelectorAll("[data-program-page]").forEach((section) => {
        section.hidden = section.dataset.programPage !== page;
    });
    document.querySelectorAll("[data-step-indicator]").forEach((step) => {
        const order = { selection: 1, draft: 2, summary: 3 };
        const stepPage = step.dataset.stepIndicator;
        step.classList.toggle("is-active", stepPage === page);
        step.classList.toggle("is-complete", order[stepPage] < order[page]);
        step.setAttribute("aria-current", stepPage === page ? "step" : "false");
    });

    renderSplitCards();
    renderScheduleOptions();
    renderSchedulePreview();
    renderDraft();
    renderSummary();
    renderGuide();
    updateControls();
}

function renderGuide() {
    const message = document.getElementById("workflow-guide-message");
    const schedulePanel = document.querySelector(".schedule-panel");
    const nextButton = document.getElementById("selection-next");
    let text = "Choose a workout split to begin.";

    if (page === "summary") {
        text = "Use the workout icons to make small changes, or Change Program to rebuild the plan.";
    } else if (page === "draft") {
        text = "Review all seven days, edit workouts as needed, then save.";
    } else if (selectedSchedule) {
        text = "Review the week, then continue to edit workouts.";
    } else if (selectedSplit) {
        text = "Now choose a weekly schedule below.";
    }

    if (message.textContent !== text) message.textContent = text;
    schedulePanel.classList.toggle("is-guided", page === "selection" && Boolean(selectedSplit) && !selectedSchedule);
    nextButton.classList.toggle("is-guided", page === "selection" && Boolean(selectedSchedule));
}

function renderSplitCards() {
    const container = document.getElementById("split-options");
    container.innerHTML = "";

    Object.entries(catalog).forEach(([id, split]) => {
        const button = document.createElement("button");
        button.type = "button";
        button.className = "split-card";
        button.setAttribute("aria-pressed", String(selectedSplit === id));
        button.disabled = isRequestPending;

        const badge = document.createElement("span");
        badge.textContent = split.shortLabel || split.label;
        const title = document.createElement("strong");
        title.textContent = split.label;
        const description = document.createElement("small");
        description.textContent = split.description || "";
        const count = document.createElement("em");
        const schedules = Object.keys(split.schedules || {}).length;
        count.textContent = `${schedules} schedule${schedules === 1 ? "" : "s"}`;
        button.append(badge, title, description, count);
        button.addEventListener("click", () => chooseSplit(id));
        container.appendChild(button);
    });
}

async function chooseSplit(id) {
    if (id === selectedSplit || isRequestPending) return;
    if (!await allowDraftReset()) return;
    selectedSplit = id;
    selectedSchedule = "";
    clearDraft();
    render();
    requestAnimationFrame(() => {
        document.querySelector(".schedule-panel")?.scrollIntoView({
            behavior: prefersReducedMotion() ? "auto" : "smooth",
            block: "center",
        });
    });
}

function renderScheduleOptions() {
    const select = document.getElementById("schedule-options");
    select.innerHTML = "";
    select.disabled = !selectedSplit || isRequestPending;
    select.appendChild(option("", selectedSplit ? "Select schedule" : "Select a split first"));

    Object.entries(catalog[selectedSplit]?.schedules || {}).forEach(([id, schedule]) => {
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
    const schedule = currentSchedule();
    const preview = document.getElementById("schedule-preview");
    const summary = document.getElementById("schedule-summary");
    preview.innerHTML = "";
    summary.textContent = schedule?.summary || "Choose a schedule to preview your week.";

    DAYS.forEach((day) => {
        const label = schedule?.days?.[day] || "Pending";
        const item = document.createElement("div");
        item.className = `preview-day${label === "Rest" ? " is-rest" : ""}`;
        const weekday = document.createElement("span");
        weekday.textContent = day.slice(0, 3);
        const focus = document.createElement("strong");
        focus.textContent = label;
        item.append(weekday, focus);
        preview.appendChild(item);
    });
}

function generateDraft() {
    draftWorkouts = [];
    let position = 0;
    DAYS.forEach((day) => {
        const label = dayLabel(day);
        if (label === "Rest" || label === "Pending") return;
        (templates[selectedSplit]?.[label] || []).forEach((workout) => {
            draftWorkouts.push({
                localKey: localKey(),
                workoutDay: day,
                workoutName: workout.name,
                workoutFocus: workout.focus || "",
                workoutSets: Number(workout.sets || 3),
                repsMin: Number(workout.reps_min || 8),
                repsMax: Number(workout.reps_max || 12),
                position: position++,
            });
        });
    });
    draftGenerated = true;
}

function renderDraft() {
    if (page !== "draft") return;
    document.getElementById("draft-selection-label").textContent =
        `${catalog[selectedSplit]?.label || ""} · ${currentSchedule()?.label || ""}`;
    renderBoard(document.getElementById("draft-board"), draftWorkouts, "draft");
}

function renderSummary() {
    if (!savedProgram) return;
    if (page === "summary") {
        selectedSplit = savedProgram.split;
        selectedSchedule = savedProgram.schedule;
    }
    document.getElementById("summary-title").textContent = catalog[savedProgram.split]?.label || savedProgram.split;
    document.getElementById("summary-schedule").textContent =
        catalog[savedProgram.split]?.schedules?.[savedProgram.schedule]?.label || savedProgram.schedule;
    if (page === "summary") renderBoard(document.getElementById("summary-board"), savedWorkouts, "summary");
}

function renderBoard(container, collection, mode) {
    const previousScroll = container.scrollLeft;
    container.innerHTML = "";
    DAYS.forEach((day) => {
        const label = dayLabel(day);
        const dayWorkouts = collection.filter((workout) => workout.workoutDay === day)
            .sort((a, b) => a.position - b.position);
        const card = document.createElement("article");
        card.className = `day-card${label === "Rest" ? " is-rest" : ""}`;

        const header = document.createElement("div");
        header.className = "day-card__header";
        const heading = document.createElement("div");
        const title = document.createElement("h3");
        title.textContent = day;
        const focus = document.createElement("p");
        focus.textContent = label;
        heading.append(title, focus);
        const count = document.createElement("span");
        count.textContent = String(dayWorkouts.length);
        header.append(heading, count);
        card.appendChild(header);

        if (label === "Rest") {
            const rest = document.createElement("p");
            rest.className = "rest-label";
            rest.textContent = "Recovery day";
            card.appendChild(rest);
        } else {
            const list = document.createElement("div");
            list.className = "workout-list";
            dayWorkouts.forEach((workout, index) => list.appendChild(workoutRow(workout, mode, index, dayWorkouts.length)));
            if (!dayWorkouts.length) {
                const empty = document.createElement("p");
                empty.className = "empty-label";
                empty.textContent = "No workouts yet.";
                list.appendChild(empty);
            }
            card.appendChild(list);

            const add = document.createElement("button");
            add.type = "button";
            add.className = "add-workout-button";
            add.textContent = "+ ADD WORKOUT";
            add.disabled = isRequestPending;
            add.addEventListener("click", () => openWorkoutForm(day, null, mode));
            card.appendChild(add);
        }
        container.appendChild(card);
    });
    container.scrollLeft = previousScroll;
    requestAnimationFrame(() => updateCarousel(container));
}

function bindCarousels() {
    document.querySelectorAll("[data-week-carousel]").forEach((carousel) => {
        const board = carousel.querySelector(".weekly-board");
        carousel.querySelector("[data-carousel-previous]")?.addEventListener("click", () => scrollCarousel(board, -1));
        carousel.querySelector("[data-carousel-next]")?.addEventListener("click", () => scrollCarousel(board, 1));

        let scrollFrame = 0;
        board.addEventListener("scroll", () => {
            cancelAnimationFrame(scrollFrame);
            scrollFrame = requestAnimationFrame(() => updateCarousel(board));
        }, { passive: true });
    });

    let resizeFrame = 0;
    window.addEventListener("resize", () => {
        cancelAnimationFrame(resizeFrame);
        resizeFrame = requestAnimationFrame(() => {
            document.querySelectorAll("[data-week-carousel] .weekly-board").forEach(updateCarousel);
        });
    }, { passive: true });
}

function carouselCards(board) {
    return [...board.querySelectorAll(".day-card")];
}

function scrollCarousel(board, direction) {
    const cards = carouselCards(board);
    if (!cards.length) return;
    const current = cards.reduce((closest, card, index) => {
        const distance = Math.abs(card.offsetLeft - board.scrollLeft);
        return distance < closest.distance ? { index, distance } : closest;
    }, { index: 0, distance: Number.POSITIVE_INFINITY });
    const targetIndex = Math.min(cards.length - 1, Math.max(0, current.index + direction));

    board.scrollTo({
        left: cards[targetIndex].offsetLeft,
        behavior: prefersReducedMotion() ? "auto" : "smooth",
    });
}

function visibleCarouselRange(board) {
    const cards = carouselCards(board);
    if (!cards.length) return { first: 0, last: 0 };
    const boardRect = board.getBoundingClientRect();
    const visible = cards.map((card, index) => {
        const rect = card.getBoundingClientRect();
        const visibleWidth = Math.max(0, Math.min(rect.right, boardRect.right) - Math.max(rect.left, boardRect.left));
        return { index, ratio: visibleWidth / Math.max(rect.width, 1) };
    }).filter((item) => item.ratio >= .35);

    if (!visible.length) return { first: 0, last: 0 };
    return { first: visible[0].index, last: visible[visible.length - 1].index };
}

function updateCarousel(board) {
    const carousel = board.closest("[data-week-carousel]");
    if (!carousel) return;
    const cards = carouselCards(board);
    const range = visibleCarouselRange(board);
    const previous = carousel.querySelector("[data-carousel-previous]");
    const next = carousel.querySelector("[data-carousel-next]");
    const progress = carousel.querySelector(".carousel-toolbar p span");
    const maxScroll = Math.max(0, board.scrollWidth - board.clientWidth);
    const atStart = board.scrollLeft <= 4;
    const atEnd = board.scrollLeft >= maxScroll - 4;
    const total = cards.length || 7;

    if (progress) {
        progress.textContent = range.first === range.last
            ? `Day ${range.first + 1} of ${total}`
            : `Days ${range.first + 1}–${range.last + 1} of ${total}`;
    }
    if (previous) previous.disabled = isRequestPending || atStart;
    if (next) next.disabled = isRequestPending || atEnd;
    carousel.classList.toggle("can-scroll-left", !atStart);
    carousel.classList.toggle("can-scroll-right", !atEnd);
}

function prefersReducedMotion() {
    return window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

function workoutRow(workout, mode, index, total) {
    const row = document.createElement("div");
    row.className = "workout-row";
    const details = document.createElement("div");
    details.className = "workout-details";
    const name = document.createElement("strong");
    name.textContent = workout.workoutName;
    const meta = document.createElement("span");
    meta.textContent = `${workout.workoutSets} sets · ${formatReps(workout)} reps${workout.workoutFocus ? ` · ${workout.workoutFocus}` : ""}`;
    details.append(name, meta);

    const actions = document.createElement("div");
    actions.className = "workout-actions";
    if (mode === "draft") {
        actions.append(
            moveButton("↑", "Move workout up", workout, -1, index === 0),
            moveButton("↓", "Move workout down", workout, 1, index === total - 1),
        );
    }
    actions.append(
        iconButton("edit", "Edit workout", () => openWorkoutForm(workout.workoutDay, workout, mode)),
        iconButton("remove", "Remove workout", () => removeWorkout(workout, mode), true),
    );
    row.append(details, actions);
    return row;
}

function moveButton(text, label, workout, direction, disabled) {
    const button = document.createElement("button");
    button.type = "button";
    button.className = "icon-action icon-action--move";
    button.textContent = text;
    button.title = label;
    button.setAttribute("aria-label", label);
    button.disabled = disabled || isRequestPending;
    button.addEventListener("click", () => moveDraftWorkout(workout, direction));
    return button;
}

function iconButton(icon, label, action, danger = false) {
    const button = document.createElement("button");
    button.type = "button";
    button.className = `icon-action${danger ? " icon-action--danger" : ""}`;
    button.title = label;
    button.setAttribute("aria-label", label);
    button.disabled = isRequestPending;
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    svg.setAttribute("viewBox", "0 -960 960 960");
    svg.setAttribute("aria-hidden", "true");
    path.setAttribute("d", icon === "edit"
        ? "M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"
        : "M200-440v-80h560v80H200Z");
    svg.appendChild(path);
    button.appendChild(svg);
    button.addEventListener("click", action);
    return button;
}

function formatReps(workout) {
    return workout.repsMin === workout.repsMax ? String(workout.repsMin) : `${workout.repsMin}–${workout.repsMax}`;
}

function moveDraftWorkout(workout, direction) {
    const sameDay = draftWorkouts.filter((item) => item.workoutDay === workout.workoutDay)
        .sort((a, b) => a.position - b.position);
    const currentIndex = sameDay.findIndex((item) => item.localKey === workout.localKey);
    const other = sameDay[currentIndex + direction];
    if (!other) return;
    const position = workout.position;
    workout.position = other.position;
    other.position = position;
    normalizePositions(draftWorkouts);
    renderDraft();
}

function bindActions() {
    document.getElementById("schedule-options").addEventListener("change", async (event) => {
        const next = event.target.value;
        if (next === selectedSchedule) return;
        if (!await allowDraftReset()) {
            event.target.value = selectedSchedule;
            return;
        }
        selectedSchedule = next;
        clearDraft();
        render();
    });
    document.getElementById("selection-next").addEventListener("click", () => {
        if (!selectedSplit || !selectedSchedule) {
            notify("warning", "Select a split and schedule first.");
            return;
        }
        if (!draftGenerated) generateDraft();
        page = "draft";
        render();
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
    document.getElementById("draft-back").addEventListener("click", () => {
        page = "selection";
        render();
    });
    document.getElementById("save-program").addEventListener("click", saveProgram);
    document.getElementById("change-program").addEventListener("click", () => {
        selectedSplit = "";
        selectedSchedule = "";
        clearDraft();
        page = "selection";
        render();
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
}

async function allowDraftReset() {
    if (!draftGenerated || !draftWorkouts.length) return true;
    return confirmAction("Changing this selection will discard your unsaved workout edits.", {
        title: "Discard draft changes?",
        confirmLabel: "Discard changes",
    });
}

function clearDraft() {
    draftWorkouts = [];
    draftGenerated = false;
}

async function saveProgram() {
    if (isRequestPending || !draftWorkouts.length) {
        if (!draftWorkouts.length) notify("warning", "Add at least one workout before saving.");
        return;
    }
    const invalid = draftWorkouts.find((workout) =>
        !workout.workoutName || workout.workoutSets < 1 || workout.repsMin < 1 || workout.repsMax < workout.repsMin);
    if (invalid) {
        notify("warning", "Review the workout names, sets, and rep ranges.");
        return;
    }

    setPending(true);
    normalizePositions(draftWorkouts);
    const result = await requestJson("/program", "POST", {
        split: selectedSplit,
        schedule: selectedSchedule,
        workouts: draftWorkouts.map((workout) => ({
            day: workout.workoutDay,
            name: workout.workoutName,
            focus: workout.workoutFocus || null,
            sets: workout.workoutSets,
            reps_min: workout.repsMin,
            reps_max: workout.repsMax,
            position: workout.position,
        })),
    });
    setPending(false);
    if (!result.success) {
        notify("error", result.message || "Unable to save program.");
        return;
    }
    savedProgram = result.program;
    savedWorkouts = result.workouts.map(normalizeSavedWorkout);
    clearDraft();
    page = "summary";
    notify("success", result.message || "Program saved.");
    render();
}

function normalizePositions(collection) {
    collection.sort((a, b) => {
        const dayDifference = DAYS.indexOf(a.workoutDay) - DAYS.indexOf(b.workoutDay);
        return dayDifference || a.position - b.position;
    }).forEach((workout, index) => {
        workout.position = index;
    });
}

function bindWorkoutForm() {
    document.getElementById("workout-form").addEventListener("submit", saveWorkout);
    document.getElementById("workout-form-cancel").addEventListener("click", closeWorkoutForm);
    document.getElementById("workout-modal").addEventListener("click", (event) => {
        if (event.target.id === "workout-modal") closeWorkoutForm();
    });
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !document.getElementById("workout-modal").hidden && !isRequestPending) closeWorkoutForm();
    });
}

function openWorkoutForm(day, workout = null, mode = "draft") {
    modalMode = mode;
    activeWorkoutKey = workout ? (mode === "draft" ? workout.localKey : workout.id) : null;
    document.getElementById("workout-form-title").textContent = workout ? "Edit workout" : "Add workout";
    document.getElementById("workout-form-day").textContent = `${day} · ${dayLabel(day)}`;
    document.getElementById("workout-day").value = day;
    document.getElementById("workout-name").value = workout?.workoutName || "";
    document.getElementById("workout-focus").value = workout?.workoutFocus || "";
    document.getElementById("workout-sets").value = workout?.workoutSets || 3;
    document.getElementById("workout-reps-min").value = workout?.repsMin || 8;
    document.getElementById("workout-reps-max").value = workout?.repsMax || 12;
    document.getElementById("workout-form-error").textContent = "";
    document.querySelector(".autocomplete-suggestions").innerHTML = "";
    document.getElementById("workout-modal").hidden = false;
    document.getElementById("workout-name").focus();
}

function closeWorkoutForm() {
    document.getElementById("workout-modal").hidden = true;
    document.getElementById("workout-form").reset();
    document.querySelector(".autocomplete-suggestions").innerHTML = "";
    activeWorkoutKey = null;
}

async function saveWorkout(event) {
    event.preventDefault();
    if (isRequestPending) return;
    const workout = {
        workoutDay: document.getElementById("workout-day").value,
        workoutName: document.getElementById("workout-name").value.trim(),
        workoutFocus: document.getElementById("workout-focus").value.trim(),
        workoutSets: Number(document.getElementById("workout-sets").value),
        repsMin: Number(document.getElementById("workout-reps-min").value),
        repsMax: Number(document.getElementById("workout-reps-max").value),
    };
    const error = document.getElementById("workout-form-error");
    if (!workout.workoutName || workout.workoutSets < 1 || workout.repsMin < 1 || workout.repsMax < workout.repsMin) {
        error.textContent = "Enter a name, positive sets, and a valid rep range.";
        notify("warning", "Review the highlighted workout details.");
        return;
    }

    if (modalMode === "draft") {
        if (activeWorkoutKey) {
            const index = draftWorkouts.findIndex((item) => item.localKey === activeWorkoutKey);
            draftWorkouts[index] = { ...draftWorkouts[index], ...workout };
        } else {
            draftWorkouts.push({ ...workout, localKey: localKey(), position: draftWorkouts.length });
        }
        normalizePositions(draftWorkouts);
        closeWorkoutForm();
        renderDraft();
        return;
    }

    const existing = activeWorkoutKey ? savedWorkouts.find((item) => item.id === activeWorkoutKey) : null;
    const url = existing ? `/program/workouts/${existing.id}` : "/program/workouts";
    setPending(true);
    const result = await requestJson(url, existing ? "PATCH" : "POST", {
        day: workout.workoutDay,
        name: workout.workoutName,
        focus: workout.workoutFocus || null,
        sets: workout.workoutSets,
        reps_min: workout.repsMin,
        reps_max: workout.repsMax,
    });
    setPending(false);
    if (!result.success) {
        error.textContent = result.message || "Unable to save workout.";
        notify("error", error.textContent);
        return;
    }
    const saved = normalizeSavedWorkout(result.workout);
    savedWorkouts = existing
        ? savedWorkouts.map((item) => item.id === saved.id ? saved : item)
        : [...savedWorkouts, saved];
    closeWorkoutForm();
    notify("success", result.message || "Workout saved.");
    render();
}

async function removeWorkout(workout, mode) {
    const confirmed = await confirmAction(`Remove ${workout.workoutName}?`, {
        title: "Remove workout",
        confirmLabel: "Remove",
    });
    if (!confirmed) return;
    if (mode === "draft") {
        draftWorkouts = draftWorkouts.filter((item) => item.localKey !== workout.localKey);
        normalizePositions(draftWorkouts);
        renderDraft();
        return;
    }

    setPending(true);
    const result = await requestJson(`/program/workouts/${workout.id}`, "DELETE");
    setPending(false);
    if (!result.success) {
        notify("error", result.message || "Unable to remove workout.");
        return;
    }
    savedWorkouts = savedWorkouts.filter((item) => item.id !== workout.id);
    normalizePositions(savedWorkouts);
    notify("info", result.message || "Workout removed.");
    render();
}

async function deleteProgram() {
    const confirmed = await confirmAction("Delete this program and every workout in it?", {
        title: "Delete program",
        confirmLabel: "Delete program",
    });
    if (!confirmed) return;
    setPending(true);
    const result = await requestJson("/program", "DELETE");
    setPending(false);
    if (!result.success) {
        notify("error", result.message || "Unable to delete program.");
        return;
    }
    savedProgram = null;
    savedWorkouts = [];
    selectedSplit = "";
    selectedSchedule = "";
    clearDraft();
    page = "selection";
    notify("info", result.message || "Program removed.");
    render();
}

function updateControls() {
    document.getElementById("selection-next").disabled = isRequestPending || !selectedSplit || !selectedSchedule;
    document.getElementById("save-program").disabled = isRequestPending || !draftWorkouts.length;
    document.querySelectorAll("button, select, input").forEach((control) => {
        if (isRequestPending && !control.closest("#toaster")) control.dataset.wasDisabled = String(control.disabled);
    });
}

function setPending(pending) {
    isRequestPending = pending;
    document.body.classList.toggle("is-program-pending", pending);
    render();
    document.getElementById("form-add").disabled = pending;
    document.getElementById("workout-form-cancel").disabled = pending;
}

async function requestJson(url, method, payload = null) {
    try {
        const response = await fetch(url, {
            method,
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
            },
            body: payload ? JSON.stringify(payload) : null,
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            return {
                success: false,
                message: Object.values(data.errors || {})[0]?.[0] || data.message || "Request failed.",
            };
        }
        return data;
    } catch {
        return { success: false, message: "Network request failed. Try again." };
    }
}

function notify(type, message) {
    if (message && typeof window.GreekGodsToast?.[type] === "function") {
        window.GreekGodsToast[type](message);
    }
}

async function confirmAction(message, options = {}) {
    if (typeof window.GreekGodsConfirm === "function") return window.GreekGodsConfirm(message, options);
    notify("warning", "Confirmation is unavailable. Try again.");
    return false;
}

function bindAutocomplete() {
    const input = document.getElementById("workout-name");
    const suggestions = document.querySelector(".autocomplete-suggestions");
    input.addEventListener("input", () => {
        const value = input.value.trim().toLowerCase();
        suggestions.innerHTML = "";
        if (!value) return;
        workoutsList.filter((name) => name.toLowerCase().includes(value)).slice(0, 6).forEach((name) => {
            const item = document.createElement("button");
            item.type = "button";
            item.textContent = name;
            item.addEventListener("click", () => {
                input.value = name;
                suggestions.innerHTML = "";
            });
            suggestions.appendChild(item);
        });
    });
}
