document.addEventListener("DOMContentLoaded", () => {
  // Set default config if not already set
  window.lexi = window.lexi || {};
  window.lexi.config = window.lexi.config || {
    testMode: {
      type: "simulation", // or "practice"
      hasTimeLimit: true,
    },
  };

  // Auto-open chatbot on desktop/tablet after 5s delay
  const isMobile = window.innerWidth < 768;
  if (!isMobile) {
    setTimeout(() => {
      window.lexi.controlChatbot(null, true, null, false, false);
    }, 5000);
  }

  // Only run on URLs containing /ielts-reading/ and /ielts-listening/
  if (
    !window.location.pathname.includes("/ielts-reading/") &&
    !window.location.pathname.includes("/ielts-listening/") &&
    !window.location.pathname.includes("/ielts-writing/")
  ) {
    return;
  }

  let currentPart = null;
  // Dectect user move between parts and inform the chatbot to get new task content
  document.querySelectorAll(".question-palette__part").forEach((partEl) => {
    setTimeout(() => {
      partEl.addEventListener("click", (event) => {
        // Skip in simulation mode - AI support is disabled during test simulation
        const mode = window.lexi.config.testMode.type;
        if (mode === "simulation") {
          return;
        }

        // Exclude clicks on .question-palette__items-group
        if (event.target.closest(".question-palette__items-group")) {
          return;
        }

        const part = partEl.getAttribute("data-part");

        // Skip if clicking the same part
        if (currentPart === part) {
          return;
        }
        currentPart = part;
        console.log("Clicked Part:", part);

        const prompt = `[System] Practice mode is on. Inform the user, in their preferred language, that you see they have switched to Part ${part}. Due to memory limitations, you need to clear the current conversation and retrieve the passage and questions for Part ${part} to save space. Ask for confirmation. If confirmed, run the function to retrieve the task content.`;
        window.lexi.controlChatbot(null, false, prompt, true, false, true);
      });
    }, 5000);
  });

  // Detect question number and send prompt to the chatbot (hint shortcut)
  document.querySelectorAll(".question__input").forEach((inputEl) => {
    inputEl.addEventListener("click", (event) => {
      // Skip on /ielts-writing/ URLs
      if (window.location.pathname.includes("/ielts-writing/")) {
        return;
      }

      const questionNum = inputEl.getAttribute("data-num");
      const questionHints =
        inputEl.getAttribute("data-hints") || "No hints available.";
      console.log("Clicked Question Input:", questionNum);

      const prompt = `Question ${questionNum}: Hints`;
      window.lexi.controlChatbot(null, false, prompt, false, false);

      const chatInput = ".mwai-input";
      window.lexi.toggleEffect(
        chatInput,
        "shimmer",
        true,
        {
          direction: "horizontal",
        },
        1000
      );
    });
  });

  // Create and manage the "Ask AI" button for text selections
  let askAIButton = null;

  const createAskAIButton = () => {
    if (askAIButton) return askAIButton;

    askAIButton = document.createElement("button");
    askAIButton.innerHTML = `
    <span class="material-symbols-rounded" style="font-size: 14px; margin-right: 4px;">add_comment</span>
    Ask AI
  `;
    askAIButton.style.cssText = `
    position: absolute;
    background: var(--lx-color-neutral-700);
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: var(--lx-rounded-md);
    cursor: pointer;
    font-size: 12px;
    font-weight: var(--lx-font-medium);
    z-index: 1000;
    box-shadow: var(--lx-shadow-md);
    display: flex;
    align-items: center;
    white-space: nowrap;
    transition: color 0.2s ease;
  `;

    // Add triangle arrow
    const triangle = document.createElement("div");
    triangle.style.cssText = `
    position: absolute;
    top: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-bottom: 6px solid var(--lx-color-neutral-700);
  `;
    askAIButton.appendChild(triangle);

    askAIButton.addEventListener("mouseover", () => {
      askAIButton.style.color = "var(--lx-color-primary-300)";
    });
    askAIButton.addEventListener("mouseout", () => {
      askAIButton.style.color = "white";
    });

    document.body.appendChild(askAIButton);
    return askAIButton;
  };

  const hideAskAIButton = () => {
    if (askAIButton) {
      askAIButton.style.display = "none";
    }
  };

  const showAskAIButton = (selectedText, rect) => {
    const button = createAskAIButton();

    button.onclick = () => {
      console.log("Selected Text:", selectedText);

      const prompt = `"${selectedText}": `;
      window.lexi.controlChatbot(null, true, prompt, false, false);

      const chatInput = ".mwai-input";
      window.lexi.toggleEffect(
        chatInput,
        "shimmer",
        true,
        {
          direction: "horizontal",
        },
        1000
      );

      hideAskAIButton();
    };

    // Position button below selection, centered
    const buttonWidth = 70; // Adjusted for smaller button
    button.style.left = `${rect.left + (rect.width - buttonWidth) / 2}px`;
    button.style.top = `${rect.bottom + window.scrollY + 8}px`; // More space for triangle
    button.style.display = "flex";
  };

  const handleTextSelection = (event) => {
    // Exclude selections within .mwai-input, .question__input, or the button itself
    if (
      event.target.closest(".mwai-input") ||
      event.target.closest(".question__input") ||
      event.target === askAIButton
    ) {
      return;
    }

    const selectedText = window.getSelection().toString().trim();
    if (selectedText.length > 0) {
      const selection = window.getSelection();
      const range = selection.getRangeAt(0);
      const rect = range.getBoundingClientRect();

      showAskAIButton(selectedText, rect);
    } else {
      hideAskAIButton();
    }
  };

  // Hide button when clicking elsewhere
  const handleClickOutside = (event) => {
    if (
      event.target !== askAIButton &&
      !window.getSelection().toString().trim()
    ) {
      hideAskAIButton();
    }
  };

  document.addEventListener("mouseup", handleTextSelection);
  document.addEventListener("click", handleClickOutside);
});
