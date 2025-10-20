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

  // Only run on URLs containing /ielts-reading/
  if (!window.location.pathname.includes("/ielts-reading/")) {
    return;
  }

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
        console.log("Clicked Part:", part);

        const prompt = `[System] Practice mode is on. Inform the user, in their preferred language, that you see they have switched to Part ${part}. Due to memory limitations, you need to clear the current conversation and retrieve the passage and questions for Part ${part} to save space. Ask for confirmation. If confirmed, run the function to retrieve the task content.`;
        window.lexi.controlChatbot(null, false, prompt, true, false, true);
      });
    }, 5000);
  });
});
