document.addEventListener("DOMContentLoaded", lexiInitializeTooltip);

function lexiInitializeTooltip() {
  // Create tooltip element
  const tooltip = document.createElement("div");
  tooltip.style.position = "absolute";
  tooltip.style.background = "black";
  tooltip.style.color = "white";
  tooltip.style.padding = "5px 10px";
  tooltip.style.borderRadius = "5px";
  tooltip.style.fontSize = "12px";
  tooltip.style.whiteSpace = "pre-wrap";
  tooltip.style.pointerEvents = "none"; // Start as non-interactive
  tooltip.style.opacity = "0";
  tooltip.style.maxWidth = "400px";
  tooltip.style.transition = "opacity 0.2s ease";
  tooltip.style.zIndex = "9999";
  tooltip.className = "lexi-tooltip";
  document.body.appendChild(tooltip);

  // Add styles for links inside tooltip
  const style = document.createElement("style");
  style.textContent = `
        .lexi-tooltip a {
            color: white;
            font-weight: bold;
        }
        .lexi-tooltip a:hover {
            text-decoration: underline;
        }
    `;
  document.head.appendChild(style);

  // Track hover state
  let isOverTarget = false;
  let isOverTooltip = false;
  let hideTimeout;
  let currentTarget = null;

  function showTooltip(target) {
    const content = target.getAttribute("data-tooltip-content");
    if (content && content.trim() !== "0") {
      tooltip.innerHTML = content.replace(/\n/g, "<br>");

      // Position tooltip under the target element
      const targetRect = target.getBoundingClientRect();
      const gap = 10;

      let left = targetRect.left + targetRect.width / 2;
      let top = targetRect.bottom + gap + window.scrollY;

      tooltip.style.left = left + "px";
      tooltip.style.top = top + "px";
      tooltip.style.opacity = "1";

      // Make tooltip interactive only when visible
      tooltip.style.pointerEvents = "auto";

      // Get tooltip dimensions after setting position
      const tooltipRect = tooltip.getBoundingClientRect();

      // Adjust horizontal position to center tooltip
      left = left - tooltipRect.width / 2;

      // Ensure tooltip stays within the right boundary
      if (left + tooltipRect.width > window.innerWidth) {
        left = window.innerWidth - tooltipRect.width - 10;
      }

      // Ensure tooltip stays within the left boundary
      if (left < 10) {
        left = 10;
      }

      tooltip.style.left = left + "px";
    }
  }

  function hideTooltip() {
    hideTimeout = setTimeout(() => {
      if (!isOverTarget && !isOverTooltip) {
        tooltip.style.opacity = "0";
        // Make tooltip non-interactive when hidden
        setTimeout(() => {
          if (tooltip.style.opacity === "0") {
            tooltip.style.pointerEvents = "none";
          }
        }, 200); // Wait for transition to complete
        currentTarget = null;
      }
    }, 100);
  }

  document.addEventListener("mouseover", function (event) {
    const target = event.target.closest("[data-tooltip-content]");
    if (target) {
      isOverTarget = true;
      currentTarget = target;
      clearTimeout(hideTimeout);
      showTooltip(target);
    }
  });

  document.addEventListener("mouseout", function (event) {
    const target = event.target.closest("[data-tooltip-content]");
    if (target) {
      isOverTarget = false;
      hideTooltip();
    }
  });

  // Handle tooltip hover
  tooltip.addEventListener("mouseenter", function () {
    isOverTooltip = true;
    clearTimeout(hideTimeout);
  });

  tooltip.addEventListener("mouseleave", function () {
    isOverTooltip = false;
    hideTooltip();
  });
}
