// Detect question number and send prompt to the chatbot (hint shortcut)
document.querySelectorAll(".question__input").forEach((inputEl) => {
  inputEl.addEventListener("click", (event) => {
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
    <span class="material-symbols-rounded" style="font-size: 14px; margin-right: 4px;">psychology</span>
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
  const triangle = document.createElement('div');
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

    hideAskAIButton();
  };

  // Position button below selection, centered
  const buttonWidth = 70; // Adjusted for smaller button
  button.style.left = `${rect.left + (rect.width - buttonWidth) / 2}px`;
  button.style.top = `${rect.bottom + window.scrollY + 8}px`; // More space for triangle
  button.style.display = "flex";
};

const handleTextSelection = (event) => {
  // Exclude selections within .mwai-input or the button itself
  if (
    event.target.closest(".mwai-input") ||
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
