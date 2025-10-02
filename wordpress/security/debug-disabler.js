console.log = function () {};
console.warn = function () {};
console.error = function () {};
// You can also override other console methods like console.info, console.debug, etc.

// Global error handler that suppresses errors
window.onerror = function (message, source, lineno, colno, error) {
  // Returning true prevents the default browser error handling.
  return true;
};

// Optionally, intercept promise rejections as well:
window.addEventListener("unhandledrejection", function (event) {
  event.preventDefault(); // This suppresses the logging of unhandled promise rejections.
});
