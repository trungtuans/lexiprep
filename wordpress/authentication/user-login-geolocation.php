<?php
/**
 * Automatically save user's geolocation country to billing_country on login
 * Add this code to your theme's functions.php or a custom plugin
 */

add_action('wp_login', 'save_geolocation_country_on_login', 10, 2);

function save_geolocation_country_on_login($user_login, $user) {
    // Check if WooCommerce is active
    if (!class_exists('WC_Geolocation')) {
        return;
    }
    
    // Get user ID
    $user_id = $user->ID;
    
    // Check if billing country already exists - skip if it does
    $existing_billing_country = get_user_meta($user_id, 'billing_country', true);
    if (!empty($existing_billing_country)) {
        return; // User already has a country set, don't override
    }
    
    // Get user's IP address
    $user_ip = WC_Geolocation::get_ip_address();
    
    // Geolocate the IP address using MaxMind
    $geo_data = WC_Geolocation::geolocate_ip($user_ip);
    
    // Get the country code
    $country_code = isset($geo_data['country']) ? $geo_data['country'] : '';
    
    // Only save if we have a valid country code
    if (!empty($country_code)) {
        // Save billing country
        update_user_meta($user_id, 'billing_country', $country_code);
        
        // Also save shipping country if it doesn't exist
        $existing_shipping_country = get_user_meta($user_id, 'shipping_country', true);
        if (empty($existing_shipping_country)) {
            update_user_meta($user_id, 'shipping_country', $country_code);
        }
        
        // Optional: Save state if available
        if (isset($geo_data['state']) && !empty($geo_data['state'])) {
            $existing_billing_state = get_user_meta($user_id, 'billing_state', true);
            if (empty($existing_billing_state)) {
                update_user_meta($user_id, 'billing_state', $geo_data['state']);
            }
            
            $existing_shipping_state = get_user_meta($user_id, 'shipping_state', true);
            if (empty($existing_shipping_state)) {
                update_user_meta($user_id, 'shipping_state', $geo_data['state']);
            }
        }
        
        // Optional: Log for debugging (remove in production)
        error_log("User {$user_id} logged in with geolocation country: {$country_code}");
    }
}