/*
Name: getTaskContent
Description: Retrieves the task content, including the passage and questions, for the active part or a specified part. This will clear the whole conversation.
- section (string|number): The section (part) number to retrieve (e.g., "1", "2", "3", or "all"). If not provided or null, defaults to the currently active part. In most cases, use "null".
*/

function getTaskContent(section = null) {
  const chatbotId = 251016;
  // Clear any existing chatbot messages
  window.lexi.controlChatbot(chatbotId, false, null, false, true);

  const taskType = getTaskType();
  if (taskType === "Unknown") {
    console.warn("Unable to determine task type from URL.");
    return null;
  }

  const activePart = getActivePart();
  // Default to active part if section not provided
  if (section === undefined || section === null) {
    // If no active part, default to "all"
    section = activePart || "all";
  }
  const taskPassage = getTaskPassage(section);
  const taskQuestions = getTaskQuestions(false);
  const taskContent = `##Task Type: ${taskType}

##Passage:
${taskPassage}

##Questions:
${taskQuestions}`;

  console.log("Task Content:\n", taskContent);

  const splitOneId = "#split-one";

  window.lexi.toggleEffect(splitOneId, "flash-border", true, {}, 2000);
  setTimeout(() => {
    window.lexi.toggleEffect(
      splitOneId,
      "shimmer",
      true,
      {
        direction: "veritical",
      },
      2000
    );
  }, 1000);

  const splitTwoId = "#split-two";
  setTimeout(() => {
    window.lexi.toggleEffect(splitTwoId, "flash-border", true, {}, 2000);
    setTimeout(() => {
      window.lexi.toggleEffect(
        splitTwoId,
        "shimmer",
        true,
        {
          direction: "veritical",
        },
        2000
      );
    }, 1000);
  }, 2000);

  // Notify the user via chatbot with delay
  setTimeout(() => {
    // Skip in simulation mode - AI support is disabled during test simulation
    const mode =
      window.lexi.config.testMode.type.charAt(0).toUpperCase() +
      window.lexi.config.testMode.type.slice(1);
    const prompt = `[Task Data]
"${taskContent}"

[System] [${mode} mode is on]
Inform the user, in their preferred language, that you can now see the passage and questions for part ${section}. Show the passage title(s) to confirm. Ask if they need any help with this part.`;
    window.lexi.controlChatbot(chatbotId, true, prompt, true, false, true);
  }, 2000);

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

  // Retrieve the active part
  function getActivePart() {
    const activePartEl = document.querySelector(
      ".question-palette__part.-active"
    );

    const part =
      activePartEl?.dataset.part ?? activePartEl?.getAttribute("data-part");

    console.log("Active Part:", part);

    return part;
  }

  // Retrieve the reading passage content from the split-one container.
  function getTaskPassage(section) {
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
}

/*
Name: getActivePartAndQuestion
Description: Retrieves the currently active part and question that the user is in.
*/

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
  const activePartAndQuestion = labels.join(", ");
  console.log("Active Part and Question:", activePartAndQuestion);

  // Notify the user via chatbot with delay
  setTimeout(() => {
    const chatbotId = 251016;
    const prompt =
      "[System] Inform the user, in their preferred language, that they are currently in " +
      activePartAndQuestion +
      ". Ask if they need help with this question";
    window.lexi.controlChatbot(chatbotId, true, prompt, true, false, true);
  }, 2000);

  return activePartAndQuestion;
}

/*
Name: setPracticeMode
Description: Activates practice mode with an optional timer. If minutes is null, activates unlimited practice mode. If minutes is specified, sets a timer for that duration.
- minutes (string|number|null): Number of minutes for the timer (e.g., 5, 30, 99). If null, activates practice mode with no time limit.
*/
function setPracticeMode(minutes = null) {
  // Set default config if not already set
  window.lexi = window.lexi || {};
  window.lexi.config = window.lexi.config || {
    testMode: {
      type: "simulation", // or "practice"
      hasTimeLimit: true,
    },
  };

  // Stop current timer if running
  if (typeof window.timer !== "undefined" && window.timer) {
    window.timer.pendingTime();
  }

  // Restore timer functionality if it was disabled (in case switching from unlimited to timed)
  if (window.runTimeClock.toString().includes("Timer disabled")) {
    delete window.runTimeClock;
  }

  const timeElement = document.querySelector("#time-clock");
  if (!timeElement) {
    console.warn("Time element not found");
    return;
  }

  const headerTimeClock = ".realtest-header__time-clock";

  // Add shimmer effect before updating content
  window.lexi.toggleEffect(headerTimeClock, "shimmer", true, {
    direction: "horizontal",
  });

  if (minutes === null) {
    // Practice mode with no time limit
    timeElement.style.display = "block";
    timeElement.setAttribute("data-timer", "0");
    timeElement.setAttribute("data-time", "0");
    timeElement.setAttribute("data-duration-default", "0");

    // Update config for unlimited practice mode
    window.lexi.config.testMode = {
      type: "practice",
      hasTimeLimit: false,
    };

    // Update the content to show practice mode with delay
    setTimeout(() => {
      // Remove shimmer effect
      window.lexi.toggleEffect(headerTimeClock, "shimmer", false, {
        direction: "horizontal",
      });

      // Add fade-in-slide effect
      window.lexi.toggleEffect(headerTimeClock, "fade-in-slide", true, {
        direction: "left-to-right",
      });

      const timeVal = timeElement.querySelector(".realtest-header__time-val");
      const timeText = timeElement.querySelector(".realtest-header__time-text");

      if (timeVal) {
        timeVal.textContent = "Practice Mode";
      }
      if (timeText) {
        timeText.textContent = "";
      }
    }, 1500);

    // Disable timer functionality
    window.runTimeClock = function () {
      console.log("Timer disabled - Practice mode");
      return false;
    };

    // Clear timer variables
    window.rtime = 0;
    if (typeof remainingTime !== "undefined") {
      remainingTime = 0;
    }

    console.log("Practice mode enabled with no time limit");

    // Notify user
    setTimeout(() => {
      const chatbotId = 251016;
      const prompt =
        "[System] Inform the user, in their preferred language, that they are now in practice mode with no time limit.";
      window.lexi.controlChatbot(chatbotId, true, prompt, true, false, true);
    }, 2000);
  } else {
    // Practice mode with timer
    const duration = minutes * 60; // Convert minutes to seconds

    timeElement.style.display = "block";
    timeElement.setAttribute("data-timer", "1");
    timeElement.setAttribute("data-time", duration);
    timeElement.setAttribute("data-duration-default", duration);
    timeElement.setAttribute("data-current-time", duration);

    // Update config for timed practice mode
    window.lexi.config.testMode = {
      type: "practice",
      hasTimeLimit: true,
    };

    // Update the content to show timer with delay
    setTimeout(() => {
      // Remove shimmer effect
      window.lexi.toggleEffect(headerTimeClock, "shimmer", false, {
        direction: "horizontal",
      });

      // Add fade-in-slide effect
      window.lexi.toggleEffect(headerTimeClock, "fade-in-slide", true, {
        direction: "left-to-right",
      });

      const timeVal = timeElement.querySelector(".realtest-header__time-val");
      const timeText = timeElement.querySelector(".realtest-header__time-text");

      if (timeVal) {
        timeVal.textContent = minutes;
      }
      if (timeText) {
        timeText.textContent = "minutes remaining";
      }
    }, 1500);

    // Start new timer
    setTimeout(() => {
      window.runTimeClock(window.timeEndReading);
    }, 100);

    // Update global variables
    window.rtime = duration;
    if (typeof remainingTime !== "undefined") {
      remainingTime = duration;
    }

    console.log(`Practice mode enabled with ${minutes} minutes timer`);

    // Notify user
    setTimeout(() => {
      const chatbotId = 251016;
      const prompt =
        "[System] Inform the user, in their preferred language, that practice mode has been activated with a " +
        minutes +
        " minute timer.";
      window.lexi.controlChatbot(chatbotId, true, prompt, true, false, true);
    }, 2000);
  }
}

// Usage examples:
// setPracticeMode();     // Practice mode with no time limit
// setPracticeMode(null); // Practice mode with no time limit
// setPracticeMode(99);   // Practice mode with 99 minutes
// setPracticeMode(30);   // Practice mode with 30 minutes
// setPracticeMode(5);    // Practice mode with 5 minutes

/*
Name: highlightText
Description: Highlights specified text terms (words, phrases, sentences) in the document with optional tooltips and smooth scrolling to the first match. Can be used to highlight single or multiple terms. Avoids highlighting terms that are under 4 characters and avoids highlighting over 1 paragraph.
Parameter:
- searchData (object): Object with term-tooltip pairs. Usage example: highlightText([["term1", "tooltip1"], ["term2", "tooltip2"]]). Use "" for no tooltip/highlight only.
*/

function highlightText(searchData, scopeSelector = "#part-1") {
  if (!window.lexi || typeof window.lexi.highlightText !== "function") {
    console.warn("Highlight function not available.");
    return;
  }
  window.lexi.highlightText(searchData, scopeSelector);
}

/*
Name: clearTextHighlights
Description: Removes all text highlights from the document.
*/
function clearTextHighlights() {
  if (!window.lexi || typeof window.lexi.clearTextHighlights !== "function") {
    console.warn("Clear highlights function not available.");
    return;
  }
  window.lexi.clearTextHighlights();
}
