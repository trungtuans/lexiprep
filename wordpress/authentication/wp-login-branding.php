<?php 
// Change WP Login Logo
function custom_login_logo() { ?>
    <style type="text/css">
        #login h1 a {
            background-image: url('/wp-content/uploads/2025/09/lexiprep-logo-dark-mode.svg');
            background-size: contain;
            background-repeat: no-repeat;
            width: 136px;
            height: fit-content;
        }
    </style>
<?php }
add_action('login_enqueue_scripts', 'custom_login_logo');

// Change logo URL (redirects to your site instead of wordpress.org)
function custom_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'custom_login_logo_url');

// Change logo title (hover text)
function custom_login_logo_url_title() {
    return get_bloginfo('name');
}
add_filter('login_headertitle', 'custom_login_logo_url_title');