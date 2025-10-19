<?php
add_action('wp_footer', function () {
    $url = $_SERVER['REQUEST_URI'];

    if (strpos($url, '/lexi-course/') !== false && strpos($url, '/ielts-listening/') !== false && strpos($url, '/result/') === false) {
        echo do_shortcode('[mwai_chatbot id="251009"]');
    }
});

add_action('wp_footer', function () {
    $url = $_SERVER['REQUEST_URI'];

    if (strpos($url, '/lexi-course/') !== false && strpos($url, '/ielts-reading/') !== false && strpos($url, '/result/') === false) {
        echo do_shortcode('[mwai_chatbot id="251016"]');
    }
});

add_action('wp_footer', function () {
    $url = $_SERVER['REQUEST_URI'];

    if (strpos($url, '/lexi-course/') !== false && strpos($url, '/ielts-writing/') !== false && strpos($url, '/result/') === false) {
        echo do_shortcode('[mwai_chatbot id="251009"]');
    }
});

add_action('wp_footer', function () {
    $url = $_SERVER['REQUEST_URI'];

    // Run everywhere EXCEPT pages containing /lexi-course/ or /result/
    if (strpos($url, '/lexi-course/') === false && strpos($url, '/result/') === false) {
        echo do_shortcode('[mwai_chatbot id="251009"]');
    }
});
