// Smooth scroll to exam detail section on pagination click
document.addEventListener("DOMContentLoaded", function () {
  document.addEventListener("click", function (e) {
    if (
      e.target.closest(
        ".exam-detail .proqyz__course-pagination .proqyz__course-page"
      )
    ) {
      setTimeout(function () {
        const examDetail = document.querySelector(".exam-detail");
        if (examDetail) {
          const targetPosition =
            examDetail.getBoundingClientRect().top + window.pageYOffset - 120;
          window.scrollTo({
            top: targetPosition,
            behavior: "smooth",
          });
        }
      }, 1000);
    }
  });
});
