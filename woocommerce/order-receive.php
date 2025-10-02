<?php
add_action("woocommerce_before_thankyou", "custom_content_thankyou", 10, 1);

function custom_content_thankyou($order_id)
{
    // Get the order object
    $order = wc_get_order($order_id);

    // Check payment method and display appropriate content
    if ($order->get_payment_method() === "bacs") {
        echo '<div id="lx-bmc-payment-steps" class="lx-bmc">
    <h3 class="lx-bmc__title">Complete Your Payment</h3>

    <div class="lx-bmc__steps">
        <div class="lx-bmc__step">
            <div class="lx-bmc__step-number">1</div>
            <div class="lx-bmc__step-content">
                <h4 class="lx-bmc__step-heading">Copy the Total Amount</h4>
                <div class="lx-bmc__amount-container">
                    <span id="lx-total-amount" class="lx-bmc__total-amount">$0.00</span>
                    <button id="lx-copy-amount" class="lx-btn lx-btn--copy" title="Copy Amount">
                        <span class="material-symbols-rounded" id="copy-icon">content_copy</span>
                        Copy
                    </button>
                </div>
            </div>
        </div>

        <div class="lx-bmc__step">
            <div class="lx-bmc__step-number">2</div>
            <div class="lx-bmc__step-content">
                <h4 class="lx-bmc__step-heading">
                    Visit Our Official Buy Me a Coffee Page
                </h4>
                <a href="https://buymeacoffee.com/lexiprep" target="_blank" rel="nofollow noopener"
                    class="lx-btn lx-btn--bmc" title="Open BuyMeACoffee">
                    <span class="material-symbols-rounded">open_in_new</span>
                    <span class="lx-btn__text">Visit BuyMeACoffee/LexiPrep</span>
                </a>
            </div>
        </div>

        <div class="lx-bmc__step">
            <div class="lx-bmc__step-number">3</div>
            <div class="lx-bmc__step-content">
                <h4 class="lx-bmc__step-heading">
                    Paste the Copied Amount to Process Your Payment
                </h4>
                <p class="lx-bmc__step-description">
                    Buy Me a Coffee does not accept decimal amounts (with commas "," or
                    periods ".") for manual input. Please paste the copied amount to
                    proceed.
                </p>
            </div>
        </div>
    </div>

    <div class="lx-bmc__note">
        <strong>Note:</strong> Please use the same email linked to your LexiPrep
        account so we can verify your transaction.
    </div>
</div>

<style>
    .lx-bmc {
        background: var(--lx-color-neutral-50);
        border-radius: var(--lx-rounded-xl);
        box-shadow: var(--lx-shadow-xs);
        padding: 40px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 40px;
        width: 100%;
        overflow: hidden;
        position: relative;
        border: 2px solid var(--lx-color-primary-500);
    }

    .lx-bmc::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--lx-color-primary-500), var(--lx-color-secondary-500));
    }

    .lx-bmc__title {
        margin: 0 0 32px !important;
        font-size: var(--lx-text-2xl) !important;
        font-weight: var(--lx-font-bold) !important;
        text-align: center !important;
        color: var(--lx-color-neutral-900) !important;
        line-height: 1.3 !important;
    }

    .lx-bmc__steps {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .lx-bmc__step {
        display: flex;
        align-items: flex-start;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--lx-color-neutral-300);
    }

    .lx-bmc__step:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .lx-bmc__step-number {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        background: var(--lx-color-primary-500);
        color: var(--lx-color-neutral-50);
        font-weight: var(--lx-font-bold);
        font-size: var(--lx-text-base);
        border-radius: var(--lx-rounded-full);
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 16px;
        box-shadow: var(--lx-shadow-sm);
    }

    .lx-bmc__step-content {
        flex-grow: 1;
    }

    .lx-bmc__step-heading {
        margin: 0 0 12px;
        font-size: var(--lx-text-lg);
        font-weight: var(--lx-font-semibold);
        color: var(--lx-color-neutral-900);
        line-height: 1.4;
    }

    .lx-bmc__step-description {
        margin: 0;
        font-size: var(--lx-text-sm);
        color: var(--lx-color-neutral-700);
        line-height: 1.6;
    }

    .lx-bmc__amount-container {
        display: flex;
        align-items: center;
        margin-top: 12px;
        gap: 12px;
    }

    .lx-bmc__total-amount {
        background: var(--lx-color-neutral-100);
        padding: 14px 18px;
        border-radius: var(--lx-rounded-lg);
        font-size: var(--lx-text-xl);
        font-weight: var(--lx-font-semibold);
        color: var(--lx-color-neutral-900);
        border: 2px solid var(--lx-color-neutral-300);
        min-width: 120px;
        text-align: center;
    }

    .lx-btn {
        padding: 12px 18px !important;
        border-radius: var(--lx-rounded-lg) !important;
        cursor: pointer !important;
        font-size: var(--lx-text-sm) !important;
        font-weight: var(--lx-font-medium) !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        outline: none !important;
        text-decoration: none !important;
        border: none !important;
        box-shadow: var(--lx-shadow-sm) !important;
    }

    .lx-btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: var(--lx-shadow-md) !important;
    }

    .lx-btn:active {
        transform: translateY(0px) !important;
        box-shadow: var(--lx-shadow-sm) !important;
    }

    .lx-btn--copy {
        background: var(--lx-color-neutral-50) !important;
        color: var(--lx-color-neutral-700) !important;
        border: 2px solid var(--lx-color-neutral-300) !important;
    }

    .lx-btn--copy:hover {
        background: var(--lx-color-neutral-100) !important;
        color: var(--lx-color-neutral-900) !important;
        border-color: var(--lx-color-neutral-500) !important;
    }

    .lx-btn--bmc {
        margin-top: 12px;
        background: var(--lx-color-primary-500) !important;
        color: var(--lx-color-neutral-50) !important;
    }

    .lx-btn--bmc:hover {
        background: var(--lx-color-primary-700) !important;
    }

    .lx-bmc .material-symbols-rounded {
        font-size: 20px;
    }

    .lx-bmc__note {
        margin-top: 32px;
        background: var(--lx-color-amber-50);
        border-left: 4px solid var(--lx-color-amber-500);
        padding: 18px;
        border-radius: var(--lx-rounded-lg);
        color: var(--lx-color-amber-800);
        font-size: var(--lx-text-sm);
        line-height: 1.6;
    }

    .lx-bmc__note strong {
        color: var(--lx-color-amber-900);
        font-weight: var(--lx-font-semibold);
    }

    @media (max-width: 640px) {
        .lx-bmc {
            padding: 24px;
            margin: 16px;
        }

        .lx-bmc__amount-container {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .lx-bmc__total-amount {
            text-align: center;
        }

        .lx-btn--bmc {
            margin-top: 0;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Function to extract the order total amount
        function extractOrderTotal() {
            // Method 1: Try to get from order details table
            let totalAmount = 0;

            // Look for the order total in the order details table
            const orderTotalElements = document.querySelectorAll(
                ".woocommerce-order-details .order-total .amount"
            );

            if (orderTotalElements.length > 0) {
                for (let i = 0; i < orderTotalElements.length; i++) {
                    let amount = orderTotalElements[i].textContent.trim();
                    // Remove currency symbols and formatting
                    amount = amount.replace(/[^\d.,]/g, "");
                    // Replace comma with dot for decimal
                    amount = amount.replace(",", ".");

                    if (!isNaN(parseFloat(amount))) {
                        totalAmount = parseFloat(amount);
                        break;
                    }
                }
            }

            // Method 2: If not found, try alternative selectors
            if (totalAmount === 0) {
                const altTotalElements = document.querySelectorAll(
                    ".woocommerce-table--order-details tfoot tr:last-child .amount"
                );

                if (altTotalElements.length > 0) {
                    for (let i = 0; i < altTotalElements.length; i++) {
                        let amount = altTotalElements[i].textContent.trim();
                        amount = amount.replace(/[^\d.,]/g, "");
                        amount = amount.replace(",", ".");

                        if (!isNaN(parseFloat(amount))) {
                            totalAmount = parseFloat(amount);
                            break;
                        }
                    }
                }
            }

            // Method 3: Try to find any element with total and amount
            if (totalAmount === 0) {
                const allAmountElements = document.querySelectorAll(".amount");
                for (let i = 0; i < allAmountElements.length; i++) {
                    const parent = allAmountElements[i].closest("tr");
                    if (parent && parent.textContent.toLowerCase().includes("total")) {
                        let amount = allAmountElements[i].textContent.trim();
                        amount = amount.replace(/[^\d.,]/g, "");
                        amount = amount.replace(",", ".");

                        if (!isNaN(parseFloat(amount))) {
                            totalAmount = parseFloat(amount);
                            break;
                        }
                    }
                }
            }

            // Format the amount with currency symbol
            let formattedAmount = "$" + totalAmount.toFixed(2);

            // Update the display
            document.getElementById("lx-total-amount").textContent = formattedAmount;
            return formattedAmount;
        }

        // Extract and display the order total on page load
        extractOrderTotal();

        // Copy amount functionality
        document
            .getElementById("lx-copy-amount")
            .addEventListener("click", function () {
                const amountText =
                    document.getElementById("lx-total-amount").textContent;

                const tempInput = document.createElement("input");
                tempInput.value = amountText;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);

                // Change icon to check
                const copyIcon = document.getElementById("copy-icon");
                copyIcon.textContent = "check";

                setTimeout(function () {
                    copyIcon.textContent = "content_copy";
                }, 3000);
            });
    });
</script>';
    } elseif ($order->get_payment_method() === "ppcp") {
        echo '<div class="lx-payment-success">
  <div class="lx-payment-success__card">
    <div class="lx-payment-success__icon-container">
      <span class="material-symbols-rounded lx-payment-success__icon"
        >check_circle</span
      >
    </div>

    <h1 class="lx-payment-success__title">Payment Processed Successfully</h1>

    <div class="lx-payment-success__content">
      <p class="lx-payment-success__message">
        Your account has been successfully upgraded. We sincerely appreciate
        your trust in LexiPrep and your valuable support!
      </p>

      <div class="lx-payment-success__divider"></div>

      <div class="lx-payment-success__info">
        <div class="lx-payment-success__info-item">
          <span class="material-symbols-rounded lx-payment-success__info-icon"
            >history</span
          >
          <p class="lx-payment-success__text">
            We offer a
            <span class="lx-payment-success__highlight"
              >7-day full refund policy</span
            >
            for any unsatisfactory purchases.
          </p>
        </div>

        <div class="lx-payment-success__info-item">
          <span class="material-symbols-rounded lx-payment-success__info-icon"
            >support_agent</span
          >
          <p class="lx-payment-success__text">
            Contact our support team via
            <a
              href="https://www.messenger.com/t/330436800155957"
              target="_blank"
              class="lx-payment-success__link"
            >
              <strong>live chat</strong>
            </a>
            or email us at
            <a
              href="mailto:support@lexiprep.com"
              target="_blank"
              class="lx-payment-success__link"
            >
              <strong>support@lexiprep.com</strong>
            </a>
          </p>
        </div>

        <div class="lx-payment-success__info-item">
          <span class="material-symbols-rounded lx-payment-success__info-icon"
            >calendar_month</span
          >
          <p class="lx-payment-success__text">
            Service hours: 8:00 AM to 11:00 PM (GMT +7)
          </p>
        </div>
      </div>
    </div>

    <a href="https://www.lexiprep.com/" class="lx-payment-success__button">
      <span class="material-symbols-rounded lx-payment-success__button-icon"
        >home</span
      >
      <span>Return to Homepage</span>
    </a>
  </div>
</div>

<style>
  .lx-payment-success {
    max-width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0px;
    box-sizing: border-box;
  }

  .lx-payment-success__card {
    width: 100%;
    max-width: 550px;
    background-color: var(--lx-color-neutral-50);
    border-radius: var(--lx-rounded-2xl);
    box-shadow: var(--lx-shadow-xl);
    padding: 48px 40px;
    text-align: center;
    box-sizing: border-box;
    position: relative;
    overflow: hidden;
    border: 1px solid var(--lx-color-neutral-300);
  }

  .lx-payment-success__card:before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, var(--lx-color-emerald-500), var(--lx-color-sky-500));
  }

  .lx-payment-success__icon-container {
    width: 80px;
    height: 80px;
    border-radius: var(--lx-rounded-full);
    background-color: var(--lx-color-emerald-100);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    box-shadow: var(--lx-shadow-sm);
  }

  .lx-payment-success__icon {
    font-size: 48px;
    color: var(--lx-color-emerald-600);
  }

  .lx-payment-success__title {
    color: var(--lx-color-neutral-900) !important;
    font-size: var(--lx-text-2xl) !important;
    font-weight: var(--lx-font-bold) !important;
    margin: 0 0 24px !important;
    line-height: 1.3 !important;
  }

  .lx-payment-success__content {
    margin-bottom: 32px;
  }

  .lx-payment-success__message {
    color: var(--lx-color-neutral-700);
    font-size: var(--lx-text-base);
    font-weight: var(--lx-font-medium);
    line-height: 1.6;
    margin-bottom: 24px;
  }

  .lx-payment-success__divider {
    height: 1px;
    background-color: var(--lx-color-neutral-300);
    margin: 24px 0;
  }

  .lx-payment-success__info {
    text-align: left;
  }

  .lx-payment-success__info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
  }

  .lx-payment-success__info-item:last-child {
    margin-bottom: 0;
  }

  .lx-payment-success__info-icon {
    margin-right: 16px;
    color: var(--lx-color-emerald-600);
    font-size: 24px;
    flex-shrink: 0;
  }

  .lx-payment-success__text {
    color: var(--lx-color-neutral-700);
    font-size: var(--lx-text-sm);
    line-height: 1.6;
    margin: 0;
    flex: 1;
  }

  .lx-payment-success__highlight {
    color: var(--lx-color-emerald-700);
    font-weight: var(--lx-font-semibold);
  }

  .lx-payment-success__link {
    color: var(--lx-color-sky-600);
    text-decoration: none;
    transition: color 0.2s ease;
  }

  .lx-payment-success__link:hover {
    color: var(--lx-color-sky-700);
    text-decoration: underline;
  }

  .lx-payment-success__button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: var(--lx-color-emerald-600);
    color: var(--lx-color-neutral-50) !important;
    text-decoration: none;
    padding: 14px 28px;
    border-radius: var(--lx-rounded-xl);
    font-size: var(--lx-text-base);
    font-weight: var(--lx-font-semibold);
    transition: all 0.3s ease;
    box-shadow: var(--lx-shadow-md);
  }

  .lx-payment-success__button:hover {
    background-color: var(--lx-color-emerald-700);
    box-shadow: var(--lx-shadow-lg);
    transform: translateY(-2px);
  }

  .lx-payment-success__button-icon {
    margin-right: 8px;
    font-size: 20px;
  }

  @media screen and (max-width: 600px) {
    .lx-payment-success__card {
      padding: 32px 24px;
      margin: 16px;
    }

    .lx-payment-success__icon-container {
      width: 64px;
      height: 64px;
    }

    .lx-payment-success__icon {
      font-size: 36px;
    }

    .lx-payment-success__title {
      font-size: var(--lx-text-xl) !important;
    }

    .lx-payment-success__message {
      font-size: var(--lx-text-sm);
    }
  }
</style>
';
    }
}

