// For Listening & Reading result page
document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.href.includes("/result/")) return;

  const buttons = document.querySelectorAll("a.btn-show-re");
  buttons.forEach((btn) => {
    if (btn.textContent.trim() === "Back to course") {
      btn.textContent = "Back to Homepage";
      btn.setAttribute(
        "onclick",
        "window.location.href='https://ielts.lexiprep.com/'"
      );
    }
  });
});

// For Writing result page
document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.href.includes("/result/")) return;

  const buttons = document.querySelectorAll(
    "button.ielts-lms-result-show-button"
  );
  buttons.forEach((btn) => {
    if (btn.textContent.trim() === "Back to the course") {
      // First, inject the AI button and text
      const aiButton = document.createElement("button");
      aiButton.innerHTML =
        '<span class="material-symbols-rounded" style="vertical-align: middle; margin-right: 6px;">auto_awesome</span>Check Score with AI';
      aiButton.className = "ielts-lms-result-show-button";
      aiButton.style.background =
        "linear-gradient(135deg, var(--lx-color-neutral-900), var(--lx-color-secondary-700))";
      aiButton.style.border = "1px solid var(--lx-color-secondary-700)";
      aiButton.style.color = "var(--lx-color-primary-300)";
      aiButton.style.marginRight = "10px";
      aiButton.setAttribute(
        "data-tooltip-content",
        "Check writing scores via our partner - LexiBot AI (www.lexibot.me)"
      );
      aiButton.onclick = () => {
        window.open(
          "https://www.lexibot.me/services/free-ielts-writing-score-checker/",
          "_blank"
        );
      };

      const infoText = document.createElement("p");
      infoText.textContent = "Via our partner - LexiBot AI";
      infoText.style.fontSize = "12px";
      infoText.style.color = "#555";
      infoText.style.marginTop = "-15px";
      infoText.style.textAlign = "center";

      btn.parentNode.insertBefore(aiButton, btn);
      btn.parentNode.insertBefore(infoText, btn);

      // Then, modify the original button
      btn.textContent = "Back to Homepage";
      btn.setAttribute(
        "onclick",
        "window.location.href='https://ielts.lexiprep.com/'"
      );
    }
  });
});
