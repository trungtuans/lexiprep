<?php
// Replace user data placeholders in AI Engine instructions before queries are sent
add_filter( 'mwai_ai_instructions', function( $instructions ) {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $user_id = $user->ID;
        
        $email = sanitize_email( $user->user_email );
        $first_name = sanitize_text_field( get_user_meta( $user_id, 'first_name', true ) );
        $last_name = sanitize_text_field( get_user_meta( $user_id, 'last_name', true ) );
        $billing_country = sanitize_text_field( get_user_meta( $user_id, 'billing_country', true ) );
    } else {
        $email = '';
        $first_name = '';
        $last_name = '';
        $billing_country = '';
    }

    // Replace all user data placeholders
    $instructions = str_replace( '{USER_EMAIL}', $email, $instructions );
    $instructions = str_replace( '{FIRST_NAME}', $first_name, $instructions );
    $instructions = str_replace( '{LAST_NAME}', $last_name, $instructions );
    $instructions = str_replace( '{BILLING_COUNTRY}', $billing_country, $instructions );
    
    return $instructions;
}, 10, 1 );