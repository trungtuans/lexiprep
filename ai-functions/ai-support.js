document.addEventListener("DOMContentLoaded", () => {
  // Only run on URLs containing /ielts-reading/ and /ielts-listening/
  if (
    !window.location.pathname.includes("/ielts-reading/") &&
    !window.location.pathname.includes("/ielts-listening/") &&
    !window.location.pathname.includes("/ielts-writing/")
  ) {
    return;
  }

  // Auto-open chatbot on desktop/tablet after 5s delay
  const isMobile = window.innerWidth < 768;
  if (!isMobile) {
    setTimeout(() => {
      window.lexi.controlChatbot(null, true, null, false, false);
    }, 5000);
  }

  // Set default config if not already set
  window.lexi = window.lexi || {};
  window.lexi.config = window.lexi.config || {
    testMode: {
      type: "simulation", // or "practice"
      hasTimeLimit: true,
    },
  };

  // Check ?mode URL parameter to see whether "practice" or "simulation" mode is set
  const urlParams = new URLSearchParams(window.location.search);
  const modeParam = urlParams.get("mode");

  if (modeParam === "practice" || !modeParam) {
    // Prompt the chatbot to set up practice mode after 3s delay
    setTimeout(() => {
      const chatbotId = null;
      const prompt = `[System] Inform the user, in their preferred language, that they are in Practice Mode. Ask them for the test duration:
    A. No time limit
    B. Custom time (30, 60, 90 minutes, etc.)`;
      window.lexi.controlChatbot(chatbotId, true, prompt, true, false, true);
    }, 3000);

    // If mode is "practice"
    let currentPart = null;

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

    // Detect user move between parts and inform the chatbot to get new task content
    function handlePartChange() {
      // Skip in simulation mode - AI support is disabled during test simulation
      const mode = window.lexi.config.testMode.type;
      if (mode === "simulation") {
        return;
      }

      const part = getActivePart();

      // Skip if clicking the same part
      if (currentPart === part) {
        return;
      }
      currentPart = part;
      console.log("Detected Part Change:", part);

      const prompt = `[System] Practice mode is on. Inform the user, in their preferred language, that you see they have switched to Part ${part}. Due to memory limitations, you need to clear the current conversation and retrieve the passage and questions for Part ${part} to save space. Ask for confirmation. If confirmed, run the function to retrieve the task content.`;
      window.lexi.controlChatbot(null, false, prompt, true, false, true);
    }

    // Listen for clicks on .question-palette__part
    document.querySelectorAll(".question-palette__part").forEach((partEl) => {
      setTimeout(() => {
        partEl.addEventListener("click", (event) => {
          // Exclude clicks on .question-palette__items-group
          if (event.target.closest(".question-palette__items-group")) {
            return;
          }
          setTimeout(() => {
            handlePartChange();
          }, 500);
        });
      }, 5000);
    });

    // Listen for clicks on .test-panel__nav-btn
    document.querySelectorAll(".test-panel__nav-btn").forEach((navBtn) => {
      setTimeout(() => {
        navBtn.addEventListener("click", () => {
          setTimeout(() => {
            handlePartChange();
          }, 500);
        });
      }, 5000);
    });

    // Detect question number and send prompt to the chatbot (hint shortcut)
    document.querySelectorAll(".question__input").forEach((inputEl) => {
      inputEl.addEventListener("click", (event) => {
        const questionNum = inputEl.getAttribute("data-num");
        const questionHints =
          inputEl.getAttribute("data-hints") || "No hints available.";
        console.log("Clicked Question Input:", questionNum);

        const prompt = `Question ${questionNum}: Hints`;

        const textarea = document.querySelector(".mwai-input-text textarea");
        if (textarea) {
          // Check if the prompt is already in the textarea
          if (textarea.value.trim() === prompt) {
            return; // Do nothing if prompt is already there
          }

          // Use native setter so frameworks like React detect the change
          const nativeSetter = Object.getOwnPropertyDescriptor(
            window.HTMLTextAreaElement.prototype,
            "value"
          ).set;
          nativeSetter.call(textarea, prompt);

          // Store the event to trigger later, but don't trigger immediately
          inputEl.pendingBubbleEvent = new Event("input", { bubbles: true });
        }

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

      // Add blur event listener to trigger the bubble event when input loses focus
      inputEl.addEventListener("blur", () => {
        const textarea = document.querySelector(".mwai-input-text textarea");
        if (textarea && inputEl.pendingBubbleEvent) {
          textarea.dispatchEvent(inputEl.pendingBubbleEvent);
          inputEl.pendingBubbleEvent = null; // Clear the pending event
        }
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
  } else {
    // If mode is "simulation"
    // Hide .mwai-chatbot-container by adding .lx-hidden
    const chatbotContainer = document.querySelector(".mwai-chatbot-container");
    if (chatbotContainer) {
      chatbotContainer.classList.add("lx-hidden");
    }

    // Disable Ctrl+F and Command+F
    document.addEventListener("keydown", (event) => {
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === "f") {
        event.preventDefault();
        alert(
          "In real tests, the Ctrl+F / Command+F (Find) shortcut is disabled."
        );
      }
    });
  }
});
