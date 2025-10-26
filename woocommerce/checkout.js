// Auto-sets billing country to 'VN' if URL contains both '/vi/' and '/checkout/' after page load
window.addEventListener("load", function () {
  setTimeout(function () {
    const url = window.location.href;
    if (url.indexOf("/vi/") !== -1 && url.indexOf("/checkout/") !== -1) {
      const countrySelect = document.getElementById("billing_country");
      if (countrySelect) {
        countrySelect.value = "VN";
        const event = new Event("change", { bubbles: true });
        countrySelect.dispatchEvent(event);
        console.log(
          "URL contains both /vi/ and /checkout/ - Country field changed to VN"
        );
      } else {
        console.warn("Billing country field not found.");
      }
    } else {
      console.log(
        "URL does not meet the criteria (/vi/ and /checkout/) - No changes made."
      );
    }
  }, 1000); // Wait 1 second after the page loads

  // Run only on page contain /checkout/
  // Wait 5s
  // Add data-tooltip-content="" to button #place_order
  // For page contain /vi/ use Vietnamese content, for the rest use English content.

  setTimeout(function () {
    const url = window.location.href;
    if (url.indexOf("/checkout/") !== -1) {
      const placeOrderBtn = document.getElementById("place_order");
      if (placeOrderBtn) {
        let tooltipContent = "";
        if (url.indexOf("/vi/") !== -1) {
          tooltipContent = `<b>Khoản thanh toán qua mã QR sẽ được hệ thống tự động xác nhận trong vài giây.</b> Tài khoản của bạn sẽ được nâng cấp với đầy đủ tính năng Pro ngay sau đó.<br><br>Nếu gặp sự cố thanh toán hoặc vấn đề liên quan đến tài khoản, vui lòng liên hệ với chúng tôi qua <a href="https://www.messenger.com/t/841096752416358" target="_blank" rel="noopener noreferrer">live chat</a> hoặc <a href="mailto:support@lexiprep.com" target="_blank" rel="noopener noreferrer">support@lexiprep.com</a>.`;
        } else {
          tooltipContent = `<b>Your payment will be confirmed automatically within seconds.</b> Once confirmed, your account will be upgraded with full Pro access.<br><br>For payment or account issues, feel free to contact us via <a href="https://www.messenger.com/t/841096752416358" target="_blank" rel="noopener noreferrer">live chat</a> or <a href="mailto:support@lexiprep.com" target="_blank" rel="noopener noreferrer">support@lexiprep.com</a>.`;
        }
        placeOrderBtn.setAttribute("data-tooltip-content", tooltipContent);
      }
    }
  }, 3000);
});
