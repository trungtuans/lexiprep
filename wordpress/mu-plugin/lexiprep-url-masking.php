<?php
/**
 * Enhanced URL Masking for spacetree -> lexiprep
 * Place in mu-plugins/ or functions.php
 */

// Hook into multiple URL generation filters for comprehensive coverage
add_filter( 'plugins_url', 'custom_comprehensive_plugin_url_mask', 10, 3 );
add_filter( 'plugin_dir_url', 'custom_comprehensive_plugin_url_mask', 10, 3 );
add_filter( 'content_url', 'custom_content_url_mask', 10, 2 );

// Also hook into output buffering to catch any remaining hardcoded URLs
add_action( 'init', 'custom_start_url_masking_buffer', 1 );
add_action( 'shutdown', 'custom_end_url_masking_buffer', 999 );

/**
 * Main plugin URL masking function
 */
function custom_comprehensive_plugin_url_mask( $url, $path = '', $plugin = '' ) {
    $original_slug = 'spacetree';
    $masked_slug = 'lexiprep';
    $original_subfolder = 'proqyz';
    $masked_subfolder = 'lexiquiz';
    
    // Replace in the URL if it contains the original plugin directory
    if ( strpos( $url, '/plugins/' . $original_slug ) !== false ) {
        $url = str_replace( '/plugins/' . $original_slug, '/plugins/' . $masked_slug, $url );
        
        // Also replace the subfolder within the plugin
        $url = str_replace( '/' . $original_subfolder . '/', '/' . $masked_subfolder . '/', $url );
    }
    
    return $url;
}

/**
 * Handle content_url filter specifically
 */
function custom_content_url_mask( $url, $path = '' ) {
    $original_slug = 'spacetree';
    $masked_slug = 'lexiprep';
    $original_subfolder = 'proqyz';
    $masked_subfolder = 'lexiquiz';
    
    // Check if this is a plugin-related content URL
    if ( strpos( $url, '/plugins/' . $original_slug ) !== false ) {
        $url = str_replace( '/plugins/' . $original_slug, '/plugins/' . $masked_slug, $url );
        
        // Also replace the subfolder
        $url = str_replace( '/' . $original_subfolder . '/', '/' . $masked_subfolder . '/', $url );
    }
    
    return $url;
}

/**
 * Start output buffering to catch any remaining hardcoded URLs
 */
function custom_start_url_masking_buffer() {
    // Only apply to frontend and admin areas, not AJAX or REST requests
    if ( ! wp_doing_ajax() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        ob_start( 'custom_mask_urls_in_output' );
    }
}

/**
 * End output buffering and process the content
 */
function custom_end_url_masking_buffer() {
    if ( ob_get_level() > 0 ) {
        ob_end_flush();
    }
}

/**
 * Process buffered output to replace any remaining URLs
 */
function custom_mask_urls_in_output( $buffer ) {
    $original_slug = 'spacetree';
    $masked_slug = 'lexiprep';
    $original_subfolder = 'proqyz';
    $masked_subfolder = 'lexiquiz';
    
    // Get the site URL for accurate replacement
    $site_url = get_site_url();
    $parsed_url = parse_url( $site_url );
    $domain = $parsed_url['scheme'] . '://' . $parsed_url['host'];
    
    // Add port if it exists
    if ( isset( $parsed_url['port'] ) ) {
        $domain .= ':' . $parsed_url['port'];
    }
    
    // Replace various URL patterns
    $patterns = array(
        // Absolute URLs
        $site_url . '/wp-content/plugins/' . $original_slug,
        $domain . '/wp-content/plugins/' . $original_slug,
        // Relative URLs
        '/wp-content/plugins/' . $original_slug,
        // Protocol-relative URLs
        '//' . $parsed_url['host'] . '/wp-content/plugins/' . $original_slug,
    );
    
    $replacements = array(
        $site_url . '/wp-content/plugins/' . $masked_slug,
        $domain . '/wp-content/plugins/' . $masked_slug,
        '/wp-content/plugins/' . $masked_slug,
        '//' . $parsed_url['host'] . '/wp-content/plugins/' . $masked_slug,
    );
    
    $buffer = str_replace( $patterns, $replacements, $buffer );
    
    // Additional replacement for the subfolder
    $buffer = str_replace( '/' . $original_subfolder . '/', '/' . $masked_subfolder . '/', $buffer );
    
    return $buffer;
}

/**
 * Additional filter for wp_enqueue_* functions
 */
add_filter( 'script_loader_src', 'custom_mask_asset_urls', 10, 2 );
add_filter( 'style_loader_src', 'custom_mask_asset_urls', 10, 2 );

function custom_mask_asset_urls( $src, $handle ) {
    $original_slug = 'spacetree';
    $masked_slug = 'lexiprep';
    $original_subfolder = 'proqyz';
    $masked_subfolder = 'lexiquiz';
    
    if ( strpos( $src, '/plugins/' . $original_slug ) !== false ) {
        $src = str_replace( '/plugins/' . $original_slug, '/plugins/' . $masked_slug, $src );
        
        // Also replace the subfolder
        $src = str_replace( '/' . $original_subfolder . '/', '/' . $masked_subfolder . '/', $src );
    }
    
    return $src;
}