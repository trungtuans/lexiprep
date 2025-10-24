// On click of any unlocked .practice-item__btn:
// - Prevent default, get its href
// - Open popup 1019 if URL has /vi/, else 1024
// - After 500ms, set #lexi-start-practice-mode href to href + mode=practice
//   and #lexi-start-simulation-mode href to href + mode=simulation

document.addEventListener("DOMContentLoaded", function () {
  // Get all practice item buttons that are not locked
  const practiceButtons = document.querySelectorAll(
    ".practice-item__btn:not(.btn-locked)"
  );

  practiceButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();

      // Get the href attribute from the clicked button
      const originalHref = this.getAttribute("href");

      if (!originalHref) return;

      // Determine which popup to open based on URL path
      const popupId = window.location.pathname.includes("/vi/") ? 1019 : 1024;

      // Open Elementor popup
      if (typeof elementorProFrontend !== "undefined") {
        elementorProFrontend.modules.popup.showPopup({ id: popupId });
      }

      // Wait 500ms for popup to open
      setTimeout(function () {
        // Create URL with mode=practice parameter
        const practiceUrl =
          originalHref +
          (originalHref.includes("?") ? "&" : "?") +
          "mode=practice";
        const simulationUrl =
          originalHref +
          (originalHref.includes("?") ? "&" : "?") +
          "mode=simulation";

        // Update the href attributes
        const practiceModeBtn = document.querySelector(
          "#lexi-start-practice-mode"
        );
        const simulationModeBtn = document.querySelector(
          "#lexi-start-simulation-mode"
        );

        if (practiceModeBtn) {
          practiceModeBtn.setAttribute("href", practiceUrl);
        }

        if (simulationModeBtn) {
          simulationModeBtn.setAttribute("href", simulationUrl);
        }
      }, 500);
    });
  });
});
