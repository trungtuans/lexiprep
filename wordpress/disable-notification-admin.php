<?php 
// Disable admin notification when a new user registers
add_filter( 'wp_send_new_user_notification_to_admin', '__return_false' );

// Disable admin notification when user changes password
add_filter( 'wp_password_change_notification_email', '__return_false' );
