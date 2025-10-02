<?php

// Hook to clear cart before adding new items - ensures only one product in cart at a time
add_filter(
    "woocommerce_add_to_cart_validation",
    "bbloomer_only_one_in_cart",
    9999
);

// Function to empty cart and allow the new product to be added
function bbloomer_only_one_in_cart($passed)
{
    wc_empty_cart();
    return $passed;
}

// Hook to modify checkout fields based on cart contents (virtual vs physical products)
add_filter("woocommerce_checkout_fields", "bbloomer_simplify_checkout_virtual");

// Function to remove unnecessary billing fields when cart contains only virtual products
function bbloomer_simplify_checkout_virtual($fields)
{
    $only_virtual = true;
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        // Check if there are non-virtual products
        if (!$cart_item["data"]->is_virtual()) {
            $only_virtual = false;
        }
    }
    if ($only_virtual) {
        unset($fields["billing"]["billing_company"]);
        unset($fields["billing"]["billing_address_1"]);
        unset($fields["billing"]["billing_address_2"]);
        unset($fields["billing"]["billing_city"]);
        unset($fields["billing"]["billing_postcode"]);
        /* unset($fields["billing"]["billing_country"]); */
        unset($fields["billing"]["billing_state"]);
        unset($fields["billing"]["billing_phone"]);
        add_filter("woocommerce_enable_order_notes_field", "__return_false");
    }
    return $fields;
}

// Remove BACS and PPCP for customers in Vietnam
add_filter(
    "woocommerce_available_payment_gateways",
    "remove_gateways_for_vietnam"
);
function remove_gateways_for_vietnam($available_gateways)
{
    // Only on the frontend checkout
    if (is_admin() || !is_checkout()) {
        return $available_gateways;
    }

    // Get the customer's country code
    $country = WC()->customer->get_billing_country();

    // Gateways to hide for Vietnam
    $to_hide = ["bacs", "ppcp"];

    if ("VN" === $country) {
        foreach ($to_hide as $gateway_id) {
            if (isset($available_gateways[$gateway_id])) {
                unset($available_gateways[$gateway_id]);
            }
        }
    }

    return $available_gateways;
}

// Only allow “sepay” for customers in Vietnam
add_filter(
    "woocommerce_available_payment_gateways",
    "restrict_sepay_to_vietnam"
);
function restrict_sepay_to_vietnam($available_gateways)
{
    // Bail out in admin or if we’re not on checkout
    if (is_admin() || !is_checkout()) {
        return $available_gateways;
    }

    // Get the customer's country (billing)
    $country = WC()->customer->get_billing_country();

    // If they're NOT in Vietnam, remove the sepay gateway
    if ("VN" !== $country && isset($available_gateways["sepay"])) {
        unset($available_gateways["sepay"]);
    }

    return $available_gateways;
}

add_filter('woocommerce_gateway_icon', 'add_bacs_icon', 10, 2);

function add_bacs_icon($icon, $gateway_id) {
    if ($gateway_id === 'bacs') {
        $logo_url = 'https://ielts.lexiprep.com/wp-content/uploads/2025/10/stripe-x-buy-me-a-coffee.svg';
        $icon = '<img src="' . esc_url($logo_url) . '" alt="BACS Logo" style="max-height: 25px; vertical-align: middle; margin-left: 10px;">';
    }
    return $icon;
}
