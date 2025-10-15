// Purpose: Provide helpers to extract reading passages, questions, and active selections from the DOM.

// Retrieve the task type (Reading, Listening, Writing) from the current URL.
function getTaskType() {
  const pathname = window.location.pathname.toLowerCase();
  const typeMap = [
    ["/ielts-listening/", "Listening"],
    ["/ielts-reading/", "Reading"],
    ["/ielts-writing/", "Writing"],
  ];

  const [, type] = typeMap.find(([slug]) => pathname.includes(slug)) ?? [
    null,
    "Unknown",
  ];
  return type;
}

console.log(getTaskType());

/*
Name: getTaskPassage
Description: Returns the reading passage as Markdown, optionally limited to a specific part.
Parameters:
- section (string|number): "all" for the full passage or a 1-based part index.
*/

// Retrieve the reading passage content from the split-one container.
function getTaskPassage(section = "all") {
  const container = document.getElementById("split-one");
  if (!container) {
    return null;
  }
  const sections = Array.from(container.querySelectorAll("section"));
  if (!sections.length) {
    return null;
  }

  const selectedSections =
    section === "all"
      ? sections
      : sections.filter((_, idx) => idx === Number(section) - 1);
  if (!selectedSections.length) {
    return null;
  }

  const convertToMarkdown =
    (window.lexi && window.lexi.htmlToMarkdown) || ((html) => html);

  const formatSection = (node, idx) => {
    const clone = node.cloneNode(true);
    // Remove explanations from the content
    clone.querySelectorAll(".proqyz__explain").forEach((el) => el.remove());
    const markdown = convertToMarkdown(clone.innerHTML.trim());
    return `## Part ${idx + 1}\n\n${markdown}`.trim();
  };

  if (section === "all") {
    const taskPassage = selectedSections
      .map((node, idx) => formatSection(node, idx))
      .join("\n\n");
    return taskPassage;
  }

  const index = Number(section) - 1;
  const taskPassage = formatSection(selectedSections[0], index);
  return taskPassage;
}

console.log(getTaskPassage("all"));

// Retrieve the question content from the split-two container.
function getTaskQuestions(cleanup = false) {
  const container = document.getElementById("split-two");
  if (!container) {
    return null;
  }
  const htmlContent = container.innerHTML.trim();
  if (!htmlContent) {
    return "";
  }

  const convertToMarkdown =
    (window.lexi && window.lexi.htmlToMarkdown) || ((html) => html);

  const taskQuestions = convertToMarkdown(htmlContent, cleanup);
  return taskQuestions;
}


console.log(getTaskQuestions(true));

// Retrieve the active part and question label from the palette.
function getActivePartAndQuestion() {
  const activePartEl = document.querySelector(
    ".question-palette__part.-active"
  );
  const selectedQuestionEl = document.querySelector(
    ".question-palette__item.is-selected"
  );

  const part =
    activePartEl?.dataset.part ?? activePartEl?.getAttribute("data-part");
  const question =
    selectedQuestionEl?.dataset.num ??
    selectedQuestionEl?.getAttribute("data-num");

  if (!part && !question) {
    return null;
  }

  const labels = [];
  if (part) labels.push(`Part ${part}`);
  if (question) labels.push(`Question ${question}`);

  return labels.join(", ");
}

console.log(getActivePartAndQuestion());

function setTimer(minutes) {
  const duration = minutes * 60; // Convert minutes to seconds

  // Restore timer functionality if it was disabled
  if (window.runTimeClock.toString().includes("Timer disabled")) {
    // Restore the original runTimeClock function
    delete window.runTimeClock;
    // The original function will be available from read.js
  }

  // Update the time-clock element attributes
  const timeElement = document.querySelector("#time-clock");
  if (timeElement) {
    timeElement.style.display = "block"; // Ensure it's visible
    timeElement.setAttribute("data-timer", "1"); // Enable timer
    timeElement.setAttribute("data-time", duration);
    timeElement.setAttribute("data-duration-default", duration);
    timeElement.setAttribute("data-current-time", duration);

    // Restore normal timer display
    const timeVal = timeElement.querySelector(".realtest-header__time-val");
    const timeText = timeElement.querySelector(".realtest-header__time-text");

    if (timeVal) {
      timeVal.textContent = minutes;
    }
    if (timeText) {
      timeText.textContent = "minutes remaining";
    }
  }

  // Stop current timer if running
  if (typeof window.timer !== "undefined" && window.timer) {
    window.timer.pendingTime();
  }

  // Start new timer
  setTimeout(() => {
    window.runTimeClock(window.timeEndReading);
  }, 100);

  // Update global variables
  window.rtime = duration;
  if (typeof remainingTime !== "undefined") {
    remainingTime = duration;
  }

  console.log(`Timer set to ${minutes} minutes`);
}

// Usage examples:
// setTimer(99);  // Set to 99 minutes
// setTimer(30);  // Set to 30 minutes
// setTimer(5);   // Set to 5 minutes

// ...existing code...

function removeTimer() {
  // Stop current timer if running
  if (typeof window.timer !== "undefined" && window.timer) {
    window.timer.pendingTime();
  }

  // Update the timer display to show practice mode
  const timeElement = document.querySelector("#time-clock");
  if (timeElement) {
    timeElement.style.display = "block"; // Keep it visible
    timeElement.setAttribute("data-timer", "0");
    timeElement.setAttribute("data-time", "0");
    timeElement.setAttribute("data-duration-default", "0");

    // Update the content to show practice mode
    const timeVal = timeElement.querySelector(".realtest-header__time-val");
    const timeText = timeElement.querySelector(".realtest-header__time-text");

    if (timeVal) {
      timeVal.textContent = "Practice Mode";
    }
    if (timeText) {
      timeText.textContent = "";
    }
  }

  // Disable timer functionality by overriding the function
  window.runTimeClock = function () {
    console.log("Timer disabled - Practice mode");
    return false;
  };

  // Clear any existing timer intervals
  if (typeof window.rtime !== "undefined") {
    window.rtime = 0;
  }
  if (typeof remainingTime !== "undefined") {
    remainingTime = 0;
  }

  console.log("Timer removed - Practice mode enabled");
}

// Usage:
// removeTimer();  // Shows "Practice Mode - No Timer" instead of hiding
