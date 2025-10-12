<?php
/**
 * Redirect Vietnamese visitors to /vi/ homepage
 * Add this code to your theme's functions.php or as a custom plugin
 */

add_action('template_redirect', 'redirect_vietnamese_visitors_to_homepage');

function redirect_vietnamese_visitors_to_homepage() {
    // Only run on the homepage (front page)
    if (!is_front_page() || is_admin()) {
        return;
    }
    
    // Check if already on /vi/ path to prevent redirect loop
    $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if (strpos($current_path, 'vi') === 0) {
        return;
    }
    
    // Check if WooCommerce geolocation is available
    if (!class_exists('WC_Geolocation')) {
        return;
    }
    
    // Get user's IP address
    $user_ip = WC_Geolocation::get_ip_address();
    
    // Geolocate the IP address
    $geo_data = WC_Geolocation::geolocate_ip($user_ip);
    
    // Get the country code
    $country_code = isset($geo_data['country']) ? strtoupper($geo_data['country']) : '';
    
    // If user is from Vietnam, redirect to /vi/
    if ($country_code === 'VN') {
        $redirect_url = home_url('/vi/');
        
        // Optional: Log for debugging (remove in production)
        error_log("Redirecting Vietnamese visitor from IP {$user_ip} to {$redirect_url}");
        
        // Perform the redirect (302 temporary redirect)
        wp_redirect($redirect_url, 302);
        exit;
    }
}
