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