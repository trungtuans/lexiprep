// START: Add LexiPrep branding to header elements
document.addEventListener("DOMContentLoaded", function () {
  document
    .querySelectorAll(".realtest-header .d-none-sm-550px")
    .forEach((el) => {
      const txt = el.textContent.trim();
      if (!txt) return;
      if (
        txt.startsWith("LexiPrep /") ||
        el.querySelector("strong")?.textContent === "LexiPrep"
      )
        return;
      el.innerHTML = "<strong>LexiPrep</strong> / " + txt;
    });
});
// END: Add LexiPrep branding to header elements
