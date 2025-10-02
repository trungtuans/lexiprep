// Auto-sets billing country to 'VN' if URL contains both '/vi/' and '/checkout/' after page load
window.addEventListener("load", function() {
  setTimeout(function() {
    const url = window.location.href;
    if (url.indexOf('/vi/') !== -1 && url.indexOf('/checkout/') !== -1) {
      const countrySelect = document.getElementById('billing_country');
      if (countrySelect) {
        countrySelect.value = 'VN';
        const event = new Event('change', { bubbles: true });
        countrySelect.dispatchEvent(event);
        console.log('URL contains both /vi/ and /checkout/ - Country field changed to VN');
      } else {
        console.warn('Billing country field not found.');
      }
    } else {
      console.log('URL does not meet the criteria (/vi/ and /checkout/) - No changes made.');
    }
  }, 1000); // Wait 1 second after the page loads
});
