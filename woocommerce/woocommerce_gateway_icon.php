<?php 
add_filter('woocommerce_gateway_icon', 'add_bacs_icon', 10, 2);

function add_bacs_icon($icon, $gateway_id) {
    if ($gateway_id === 'bacs') {
        $logo_url = 'https://ielts.lexiprep.com/wp-content/uploads/2025/10/stripe-x-buy-me-a-coffee.svg';
        $icon = '<img src="' . esc_url($logo_url) . '" alt="BACS Logo" style="max-height: 25px; vertical-align: middle; margin-left: 10px;">';
    }
    return $icon;
}
