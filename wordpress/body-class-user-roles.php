<?php
// Add user role classes to <body> safely (works when cache skips logged-in users)
ai-function-calling/reading// This can be used in JavaScript for detecting user roles or plan status
add_filter( 'body_class', function( $classes ) {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();

        if ( ! empty( $user->roles ) ) {
            foreach ( $user->roles as $role ) {
                // sanitize to guarantee a valid HTML class
                $classes[] = 'role-' . sanitize_html_class( $role );
            }
        } else {
            $classes[] = 'role-user';
        }
    } else {
        $classes[] = 'role-guest';
    }

    // avoid duplicates
    return array_values( array_unique( $classes ) );
});
