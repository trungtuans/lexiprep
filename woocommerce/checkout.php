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

// Add custom icon to BACS payment method
add_filter('woocommerce_gateway_icon', 'add_bacs_icon', 10, 2);

function add_bacs_icon($icon, $gateway_id) {
    if ($gateway_id === 'bacs') {
        $logo_url = 'https://ielts.lexiprep.com/wp-content/uploads/2025/10/stripe-x-buy-me-a-coffee.svg';
        $icon = '<img src="' . esc_url($logo_url) . '" alt="BACS Logo" style="max-height: 25px; vertical-align: middle; margin-left: 10px;">';
    }
    return $icon;
}

// Auto-fill email field with logged-in user's email and make it readonly
add_action('woocommerce_checkout_init', 'auto_fill_checkout_email');
function auto_fill_checkout_email($checkout) {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;
        
        // Set default value for billing email
        $checkout->__set('billing_email', $user_email);
    }
}

// Modify the email field to add tooltip and make it readonly
add_filter('woocommerce_checkout_fields', 'modify_checkout_email_field');
function modify_checkout_email_field($fields) {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;
        
        // Modify billing email field
        $fields['billing']['billing_email']['default'] = $user_email;
        // Set tooltip content based on URL language
        $tooltip_content = (strpos($_SERVER['REQUEST_URI'], '/vi/') !== false) 
            ? 'Email này được liên kết và cố định với tài khoản của bạn. Để nâng cấp cho email khác, vui lòng đăng xuất và đăng nhập bằng email đó.'
            : 'This email is linked to your account and can\'t be changed. To upgrade with a different email, please log out and sign in using that email instead.';
        
        $fields['billing']['billing_email']['custom_attributes'] = array(
            'readonly' => 'readonly',
            'data-tooltip-content' => $tooltip_content
        );
    }
    
    return $fields;
}

// Add CSS to style the readonly field (optional)
add_action('wp_head', 'checkout_email_readonly_styles');
function checkout_email_readonly_styles() {
    if (strpos($_SERVER['REQUEST_URI'], '/checkout/') !== false) {
        ?>
        <style>
        input[readonly] {
            background-color: #f9f9f9 !important;
            cursor: not-allowed !important;
            opacity: 0.7 !important;
        }
        </style>
        <?php
    }
}

// Validate that email hasn't been tampered with (security measure)
add_action('woocommerce_checkout_process', 'validate_checkout_email_security');
function validate_checkout_email_security() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;
        $submitted_email = $_POST['billing_email'];
        
        if ($submitted_email !== $user_email) {
            wc_add_notice('Email address cannot be modified during checkout.', 'error');
        }
    }
}
