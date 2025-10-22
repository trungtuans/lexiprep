/*
Name: getTaskContentListening
Description: Retrieves the task content, including the transcript and questions, for the active part or a specified part. This will clear the whole conversation.
- section (string|number): The section (part) number to retrieve (e.g., "1", "2", "3", or "all"). If not provided or null, defaults to the currently active part. In most cases, use "null".
*/

function getTaskContentListening(section = null) {
  const chatbotId = null;
  // Clear any existing chatbot messages
  window.lexi.controlChatbot(chatbotId, false, null, false, true);

  const taskType = getTaskType();
  if (taskType === "Unknown") {
    console.warn("Unable to determine task type from URL.");
    return null;
  }

  let activePart = getActivePart();
  // Convert activePart to number
  if (activePart !== null) {
    activePart = Number(activePart);
  }

  // Default to active part if section not provided
  if (section === undefined || section === null) {
    // If no active part, default to "all"
    section = activePart || "all";
  }
  const taskTranscript = getTaskTranscript(section);
  const taskQuestions = getTaskQuestions(false, section);
  const taskContent = `##Task Type: ${taskType}

##Transcript:
${taskTranscript}

##Questions:
${taskQuestions}`;

  console.log("Task Content:\n", taskContent);

  const testContentContainer = ".take-test__questions-wrap";

  window.lexi.toggleEffect(
    testContentContainer,
    "flash-border",
    true,
    {},
    2000
  );
  setTimeout(() => {
    window.lexi.toggleEffect(
      testContentContainer,
      "shimmer",
      true,
      {
        direction: "veritical",
      },
      2000
    );
  }, 1000);

  // Notify the user via chatbot with delay
  setTimeout(() => {
    // Skip in simulation mode - AI support is disabled during test simulation
    const mode =
      window.lexi.config.testMode.type.charAt(0).toUpperCase() +
      window.lexi.config.testMode.type.slice(1);
    const prompt = `[Task Data]
"${taskContent}"

[System] [${mode} mode is on]
Inform the user, in their preferred language, that you can now see the audio transcript and questions for part ${section}. Show the passage title(s) to confirm. Ask if they need any help with this part. List down something that you can do.`;
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

  // Retrieve the listening transcript content for a specific section.
  function getTaskTranscript(section) {
    try {
      const transcript =
        window.lexiTranscript ||
        JSON.parse(sessionStorage.getItem("listening_transcript") || "[]");

      if (!Array.isArray(transcript)) {
        console.warn("Invalid transcript format");
        return "Transcript not available";
      }

      const sectionData = transcript.find((item) => item.section === section);

      if (sectionData) {
        console.log(`Section ${section} transcript found`);
        let sectionDataHtml = sectionData.html || "";
        sectionDataHtml = window.lexi.htmlToMarkdown(
          sectionDataHtml,
          (cleanup = true)
        );

        // Clean up extra newlines and spaces
        sectionDataHtml = sectionDataHtml
          .replace(/\n{2,}/g, "\n")
          .replace(/\u00A0/g, " ")
          .replace(/\s{2,}/g, " ");

        console.log("Transcript:\n", sectionDataHtml);

        return sectionDataHtml || "No content available";
      } else {
        console.log(`Section ${section} not found`);
        return "Transcript not found";
      }
    } catch (error) {
      console.error("Error retrieving transcript:", error);
      return "Error loading transcript";
    }
  }

  // Retrieve the question content for a specific section.
  function getTaskQuestions(cleanup = true, section = null) {
    const container = document.querySelector(".take-test__questions-wrap");
    if (!container) {
      return null;
    }

    let targetContainer = container;

    // If section is specified and not "all", target specific part questions
    if (section && section !== "all") {
      const partQuestionsContainer = container.querySelector(
        `#part-questions-${section}`
      );
      if (partQuestionsContainer) {
        targetContainer = partQuestionsContainer;
      }
    }

    const htmlContent = targetContainer.innerHTML.trim();
    if (!htmlContent) {
      return "";
    }

    const convertToMarkdown =
      (window.lexi && window.lexi.htmlToMarkdown) || ((html) => html);

    let taskQuestions = convertToMarkdown(htmlContent, cleanup);
    // Clean up extra newlines and spaces
    taskQuestions = taskQuestions
      .replace(/\n{2,}/g, "\n")
      .replace(/\u00A0/g, " ")
      .replace(/\s{2,}/g, " ");

    console.log("Task Questions:\n", taskQuestions);
    return taskQuestions;
  }
}

/*
Name: getTaskContentReading
Description: Retrieves the task content, including the passage and questions, for the active part or a specified part. This will clear the whole conversation.
- section (string|number): The section (part) number to retrieve (e.g., "1", "2", "3", or "all"). If not provided or null, defaults to the currently active part. In most cases, use "null".
*/

function getTaskContentReading(section = null) {
  const chatbotId = null;
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
  const taskContent1 = getTaskContent1(section);
  const taskContent2 = getTaskContent2(false, section);
  const taskContent = `##Task Type: ${taskType}

##Passage:
${taskContent1}

##Questions:
${taskContent2}`;

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
Inform the user, in their preferred language, that you can now see the passage and questions for part ${section}. Show the passage title(s) to confirm. Ask if they need any help with this part. List down something that you can do.`;
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
  function getTaskContent1(section) {
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
      const taskContent1 = selectedSections
        .map((node, idx) => formatSection(node, idx))
        .join("\n\n");
      return taskContent1;
    }

    const index = Number(section) - 1;
    let taskContent1 = formatSection(selectedSections[0], index);
    // Clean up extra newlines and spaces
    taskContent1 = taskContent1
      .replace(/\n{2,}/g, "\n")
      .replace(/\u00A0/g, " ")
      .replace(/\s{2,}/g, " ");
    console.log("Task Passage:\n", taskContent1);
    return taskContent1;
  }

  // Retrieve the question content from the split-two container.
  function getTaskContent2(cleanup = false, section = null) {
    const container = document.getElementById("split-two");
    if (!container) {
      return null;
    }

    let targetContainer = container;

    // If section is specified and not "all", target specific part questions
    if (section && section !== "all") {
      const partQuestionsContainer = container.querySelector(
        `#part-questions-${section}`
      );
      if (partQuestionsContainer) {
        targetContainer = partQuestionsContainer;
      }
    }

    const htmlContent = targetContainer.innerHTML.trim();
    if (!htmlContent) {
      return "";
    }

    const convertToMarkdown =
      (window.lexi && window.lexi.htmlToMarkdown) || ((html) => html);

    let taskContent2 = convertToMarkdown(htmlContent, cleanup);
    // Clean up extra newlines and spaces
    taskContent2 = taskContent2
      .replace(/\n{2,}/g, "\n")
      .replace(/\u00A0/g, " ")
      .replace(/\s{2,}/g, " ");

    console.log("Task Questions:\n", taskContent2);
    return taskContent2;
  }
}

/*
Name: getTaskContentWriting
Description: Retrieves the task content for the active part or a specified part. This will clear the whole conversation.
- section (string|number): The section (part) number to retrieve (e.g., "1", "2", "3", or "all"). If not provided or null, defaults to the currently active part. In most cases, use "null".
*/

function getTaskContentWriting(section = null) {
  const chatbotId = null;
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
  const taskContent1 = getTaskContent1(section);
  const taskContent2 = getTaskContent2(false, section);
  const taskContent = `##Task Type: ${taskType}

##Question/Topic:
${taskContent1}`;

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

  /*const splitTwoId = "#split-two";
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
  }, 2000);*/

  // Notify the user via chatbot with delay
  setTimeout(() => {
    // Skip in simulation mode - AI support is disabled during test simulation
    const mode =
      window.lexi.config.testMode.type.charAt(0).toUpperCase() +
      window.lexi.config.testMode.type.slice(1);
    const prompt = `[Task Data]
"${taskContent}"

[System] [${mode} mode is on]
Inform the user, in their preferred language, that you can now see the question/topic for part ${section}. Summarize the question in one sentence to confirm. Ask if they need any help with this part. List down something that you can do.`;
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
  function getTaskContent1(section) {
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
      const taskContent1 = selectedSections
        .map((node, idx) => formatSection(node, idx))
        .join("\n\n");
      return taskContent1;
    }

    const index = Number(section) - 1;
    let taskContent1 = formatSection(selectedSections[0], index);
    // Clean up extra newlines and spaces
    taskContent1 = taskContent1
      .replace(/\n{2,}/g, "\n")
      .replace(/\u00A0/g, " ")
      .replace(/\s{2,}/g, " ");
    console.log("Task Passage:\n", taskContent1);
    return taskContent1;
  }

  // Retrieve the question content from the split-two container.
  function getTaskContent2(cleanup = false, section = null) {
    const container = document.getElementById("split-two");
    if (!container) {
      return null;
    }

    let targetContainer = container;

    // If section is specified and not "all", target specific part questions
    if (section && section !== "all") {
      const partQuestionsContainer = container.querySelector(
        `#part-questions-${section}`
      );
      if (partQuestionsContainer) {
        targetContainer = partQuestionsContainer;
      }
    }

    const htmlContent = targetContainer.innerHTML.trim();
    if (!htmlContent) {
      return "";
    }

    const convertToMarkdown =
      (window.lexi && window.lexi.htmlToMarkdown) || ((html) => html);

    let taskContent2 = convertToMarkdown(htmlContent, cleanup);
    // Clean up extra newlines and spaces
    taskContent2 = taskContent2
      .replace(/\n{2,}/g, "\n")
      .replace(/\u00A0/g, " ")
      .replace(/\s{2,}/g, " ");

    console.log("Task Questions:\n", taskContent2);
    return taskContent2;
  }
}

/*
Name: getActiveWritingAnswerContent
Description: Retrieves the text content from the active writing answer textarea.
*/
function getActiveWritingAnswerContent() {
  const answerTextarea = document.querySelector(
    ".test-panel.-show textarea.question__input"
  );
  if (!answerTextarea) {
    console.warn("Writing answer textarea not found");
    return null;
  }

  const answerContent = answerTextarea.value.trim();
  console.log("Writing Answer Content:\n", answerContent);

  // Retrieve the word count - text content of .writing-box__words-count
  const wordCountEl = document.querySelector(".writing-box__words-count");
  const wordCountText = wordCountEl ? wordCountEl.textContent.trim() : "N/A";
  console.log("Writing Answer Word Count:", wordCountText);

  // Highlight the textarea with effect
  const testPanelSelector = ".test-panel.-show";
  const textareaSelector = ".test-panel.-show textarea.question__input";

  window.lexi.toggleEffect(textareaSelector, "flash-border", true, {}, 2000);
  setTimeout(() => {
    window.lexi.toggleEffect(
      testPanelSelector,
      "shimmer",
      true,
      {
        direction: "veritical",
      },
      2000
    );
  }, 1000);

  // Notify user
  setTimeout(() => {
    const chatbotId = null;
    const prompt = `[System]
Retrieved writing answer:
"${answerContent}"
"${wordCountText}"
Inform the user, in their preferred language, that you can now see their writing answer content. Ask if they need any help with this.`;
    console.log("Prompt:", prompt);
    window.lexi.controlChatbot(chatbotId, true, prompt, true, false, true);
  }, 2000);

  return answerContent;
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
    const chatbotId = null;
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

  // If the url contains /ielts-listening/
  if (window.location.pathname.includes("/ielts-listening/")) {
    const audioPlayer = ".take-test__player-wrap";
    // Add shimmer effect to audio player
    window.lexi.toggleEffect(
      audioPlayer,
      "shimmer",
      true,
      {
        direction: "horizontal",
      },
      2000
    );

    // Show audio seek bar in practice mode
    audioSeekBarControl("show");

    // Delay 3000ms to run getTaskContentReading
    setTimeout(() => {
      getTaskContentListening();
    }, 3000);
  }

  // If the url contains /ielts-reading/
  if (window.location.pathname.includes("/ielts-reading/")) {
    // Delay 3000ms to run getTaskContentReading
    setTimeout(() => {
      getTaskContentReading();
    }, 3000);
  }

  // If the url contains /ielts-writing/
  if (window.location.pathname.includes("/ielts-writing/")) {
    setTimeout(() => {
      getTaskContentWriting();
    }, 3000);
  }

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
    /* setTimeout(() => {
      const chatbotId = null;
      const prompt =
        "[System] Inform the user, in their preferred language, that they are now in practice mode with no time limit.";
      window.lexi.controlChatbot(chatbotId, true, prompt, true, false, true);
    }, 2000); */
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
    /* setTimeout(() => {
      const chatbotId = null;
      const prompt =
        "[System] Inform the user, in their preferred language, that practice mode has been activated with a " +
        minutes +
        " minute timer.";
      window.lexi.controlChatbot(chatbotId, true, prompt, true, false, true);
    }, 2000); */
  }

  function audioSeekBarControl(action) {
    const PLAYER_SELECTOR = "#take-test__player";
    const FULL_CONTROLS = [
      "play-large",
      "rewind",
      "play",
      "fast-forward",
      "current-time",
      "progress",
      "mute",
      "volume",
    ];
    const NO_SEEK_CONTROLS = ["mute", "volume", "current-time"];

    function getPlayerElement() {
      return document.querySelector(PLAYER_SELECTOR);
    }

    function getCurrentPlayer() {
      return window.listeningPlayer;
    }

    function savePlayerState(player) {
      if (!player) return {};

      return {
        currentTime: player.currentTime || 0,
        paused: player.paused !== false,
        volume: player.volume || 1,
        muted: player.muted || false,
        speed: player.speed || 1,
      };
    }

    function restorePlayerState(player, state) {
      if (!player || !state) return;

      try {
        player.volume = state.volume;
        player.muted = state.muted;
        player.currentTime = state.currentTime;
        if (typeof player.speed !== "undefined") {
          player.speed = state.speed;
        }
        if (!state.paused) {
          player.play();
        }
      } catch (e) {
        console.warn("Could not restore player state:", e);
      }
    }

    function createPlayer(controls) {
      const playerElement = getPlayerElement();
      if (!playerElement || typeof Plyr === "undefined") {
        console.warn("Player element or Plyr not found");
        return null;
      }

      const config = {
        controls: controls,
        hideControls: true,
        settings: [],
        seekTime: 5,
        youtube: {
          noCookie: true,
        },
      };

      return new Plyr(PLAYER_SELECTOR, config);
    }

    function updateDataAttribute(hasSeekBar) {
      jQuery(PLAYER_SELECTOR).attr(
        "data-audio-controls",
        hasSeekBar ? "1" : "0"
      );
    }

    function recreatePlayerWithControls(controls) {
      const currentPlayer = getCurrentPlayer();
      const savedState = savePlayerState(currentPlayer);

      // Destroy current player
      if (currentPlayer && typeof currentPlayer.destroy === "function") {
        currentPlayer.destroy();
      }

      // Update data attribute
      updateDataAttribute(controls.includes("progress"));

      // Create new player
      const newPlayer = createPlayer(controls);
      if (newPlayer) {
        window.listeningPlayer = newPlayer;
        restorePlayerState(newPlayer, savedState);
      }

      return newPlayer;
    }

    function isSeekBarVisible() {
      return jQuery(PLAYER_SELECTOR).data("audio-controls") == 1;
    }

    // Main action handler
    switch (action) {
      case "show":
        return recreatePlayerWithControls(FULL_CONTROLS);

      case "hide":
        return recreatePlayerWithControls(NO_SEEK_CONTROLS);

      case "toggle":
        const controls = isSeekBarVisible() ? NO_SEEK_CONTROLS : FULL_CONTROLS;
        return recreatePlayerWithControls(controls);

      case "status":
        return isSeekBarVisible() ? "visible" : "hidden";

      default:
        console.warn(
          'AudioSeekBar: Invalid action. Use "show", "hide", "toggle", or "status"'
        );
        return null;
    }
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
Description: Highlights specified text terms in the document with optional tooltips and smooth scrolling to the first match. Can be used to highlight single or multiple terms. Avoids highlighting terms that are under 4 characters and avoids highlighting over 1 paragraph.
Parameter:
- searchData (object): Object with term-tooltip pairs. Usage example: highlightText([["term1", "tooltip1"], ["term2", "tooltip2"]]). Use "" for no tooltip/highlight only.
- scopeSelector (string|null): Optional selector to limit the search area. Supports a single simple selector only: "#id", ".class", or "tag". For examaple highlightText("term", "#split-one") limits to element with id "split-one".
*/

function highlightText(searchData, scopeSelector = "#split-one") {
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
