// This should run after DOM document.addEventListener("DOMContentLoaded"
// Run in all cases exept when body contains class: .role_pmpro_role_1 or .role_pmpro_role_2 or .role_pmpro_role_3

document.addEventListener("DOMContentLoaded", function () {
  // Check if body has premium role classes and exit if found
  const body = document.body;
  if (
    body.classList.contains("role-pmpro_role_1") ||
    body.classList.contains("role-pmpro_role_2") ||
    body.classList.contains("role-pmpro_role_3") ||
    body.classList.contains("role-guest")
  ) {
    return; // Exit early for premium users or guests (ensure guests can still see the test content)
  }

  // Function to modify practice buttons based on pagination
  function modifyPracticeButtons() {
    // Get current page from pagination using proqyz__course-cp class
    let currentPage = 1;

    const currentPageElement = document.querySelector(
      ".proqyz__course-page.proqyz__course-cp"
    );
    if (currentPageElement) {
      currentPage =
        parseInt(currentPageElement.getAttribute("data-page-index")) || 1;
    }

    console.log("Current page detected:", currentPage); // Debug log

    // Get all practice buttons in exam-detail
    const practiceButtons = document.querySelectorAll(
      ".exam-detail .practice-item__btn"
    );

    console.log("Total buttons found:", practiceButtons.length); // Debug log

    // Determine how many buttons to skip (first 4 on page 1, 0 on other pages)
    const skipCount = currentPage === 1 ? 6 : 0;

    console.log("Skipping first", skipCount, "buttons"); // Debug log

    // Determine pricing URL based on current page URL
    const pricingUrl = window.location.href.includes("/vi/")
      ? "/vi/bang-gia/"
      : "/pricing";

    // Modify buttons starting from skipCount
    practiceButtons.forEach((btn, index) => {
      if (index >= skipCount) {
        // Modify href to appropriate pricing page
        btn.setAttribute("href", pricingUrl);

        // Add btn-locked class if not already present
        if (!btn.classList.contains("btn-locked")) {
          btn.classList.add("btn-locked");
        }

        // Change text to "Upgrade to Pro"
        btn.textContent = "Upgrade to Pro";
      }
    });
  }

  // Run on page load
  modifyPracticeButtons();

  // Observe pagination clicks to re-run modification
  const paginationContainer = document.querySelector(
    ".proqyz__course-pagination"
  );
  if (paginationContainer) {
    paginationContainer.addEventListener("click", function (e) {
      if (e.target.closest(".proqyz__course-page")) {
        // Wait for loading to complete, with fallback timeout
        setTimeout(modifyPracticeButtons, 1000);
      }
    });
  }

  // Alternative: Use MutationObserver to detect when pagination loading finishes
  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      if (mutation.target.classList.contains("proqyz__course-page")) {
        // Check if proqyz__page-loadiing class was removed (loading finished)
        if (
          mutation.oldValue &&
          mutation.oldValue.includes("proqyz__page-loadiing") &&
          !mutation.target.classList.contains("proqyz__page-loadiing")
        ) {
          console.log("Page loading finished, modifying buttons"); // Debug log
          modifyPracticeButtons();
        }
      }
    });
  });

  if (paginationContainer) {
    observer.observe(paginationContainer, {
      attributes: true,
      attributeFilter: ["class"],
      attributeOldValue: true,
      subtree: true,
    });
  }
});
