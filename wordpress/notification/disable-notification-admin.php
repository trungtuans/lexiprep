<?php 
// Disable admin notification when a new user registers
add_filter( 'wp_send_new_user_notification_to_admin', '__return_false' );
