<?php
add_action('wp_footer', function () {
    $url = $_SERVER['REQUEST_URI'];

    if (strpos($url, '/lexi-course/') !== false && strpos($url, '/ielts-listening/') !== false) {
        echo do_shortcode('[mwai_chatbot id="251009"]');
    }
});

add_action('wp_footer', function () {
    $url = $_SERVER['REQUEST_URI'];

    if (strpos($url, '/lexi-course/') !== false && strpos($url, '/ielts-reading/') !== false) {
        echo do_shortcode('[mwai_chatbot id="251009"]');
    }
});

add_action('wp_footer', function () {
    $url = $_SERVER['REQUEST_URI'];

    if (strpos($url, '/lexi-course/') !== false && strpos($url, '/ielts-writing/') !== false) {
        echo do_shortcode('[mwai_chatbot id="251009"]');
    }
});

add_action('wp_footer', function () {
    $url = $_SERVER['REQUEST_URI'];

    // Run everywhere EXCEPT pages containing /lexi-course/
    if (strpos($url, '/lexi-course/') === false) {
        echo do_shortcode('[mwai_chatbot id="251009"]');
    }
});
