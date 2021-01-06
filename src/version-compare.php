<?php

$errors = function ($message, $subtitle = '', $title = '') {
    $title   = $title ? esc_html__('Error', WCPROTOSL_TEXT_DOMAIN) : '';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p>";
    wp_die($message);
};

/** Ensure PHP version compatibility */
if (version_compare('7.4', phpversion(), '>=')) {
    $errors(
        esc_html__('Please install 7.4 or higher', WCPROTOSL_TEXT_DOMAIN),
        esc_html__('Incompatible PHP version', WCPROTOSL_TEXT_DOMAIN)
    );
}

/** Ensure WordPress version compatibility*/
if (version_compare('5.1', get_bloginfo('version'), '>=')) {
    $errors(
        esc_html__('Please install 5.1 or higher', WCPROTOSL_TEXT_DOMAIN),
        esc_html__('Incompatible WordPress version', WCPROTOSL_TEXT_DOMAIN)
    );
}
