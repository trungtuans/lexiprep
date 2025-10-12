
document.addEventListener("DOMContentLoaded", lxInitializeTooltip);

function lxInitializeTooltip() {
    // Create tooltip element
    const tooltip = document.createElement("div");
    tooltip.style.position = "absolute";
    tooltip.style.background = "black";
    tooltip.style.color = "white";
    tooltip.style.padding = "5px 10px";
    tooltip.style.borderRadius = "5px";
    tooltip.style.fontSize = "12px";
    tooltip.style.whiteSpace = "pre-wrap"; // Allows line breaks
    tooltip.style.pointerEvents = "none";
    tooltip.style.opacity = "0";
    tooltip.style.maxWidth = "400px";
    tooltip.style.transition = "opacity 0.2s ease";
    tooltip.style.zIndex = "9999";
    document.body.appendChild(tooltip);

    document.addEventListener("mouseover", function (event) {
        const target = event.target.closest("[data-tooltip-content]");
        if (target) {
            const content = target.getAttribute("data-tooltip-content");
            if (content && content.trim() !== "0") {
                tooltip.innerHTML = content.replace(/\n/g, "<br>"); // Support line breaks

                // Position tooltip under the target element
                const targetRect = target.getBoundingClientRect();
                const gap = 10;

                let left = targetRect.left + (targetRect.width / 2);
                let top = targetRect.bottom + gap + window.scrollY;

                // Center the tooltip horizontally relative to the target
                tooltip.style.left = left + "px";
                tooltip.style.top = top + "px";
                tooltip.style.opacity = "1";

                // Get tooltip dimensions after setting position
                const tooltipRect = tooltip.getBoundingClientRect();

                // Adjust horizontal position to center tooltip
                left = left - (tooltipRect.width / 2);

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
    });

    document.addEventListener("mouseout", function (event) {
        if (event.target.closest("[data-tooltip-content]")) {
            tooltip.style.opacity = "0";
        }
    });
}