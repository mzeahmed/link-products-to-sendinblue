<?php

/**
 * Testing the compatibility of PHP and WordPress versions
 *
 * @param          $message
 * @param  string  $subtitle
 * @param  string  $title
 *
 * @since 1.0.0
 */
$errors = function ($message, string $subtitle = '', string $title = '') {
    $title   = $title ? esc_html__('Error', LPTS_TEXT_DOMAIN) : '';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p>";
    wp_die($message);
};

/** Ensure PHP version compatibility */
if (version_compare('7.1', phpversion(), '>=')) {
    $errors(
        esc_html__('Please install 7.1 or higher', LPTS_TEXT_DOMAIN),
        esc_html__('Incompatible PHP version', LPTS_TEXT_DOMAIN)
    );
}

/** Ensure WordPress version compatibility*/
if (version_compare('5.1', get_bloginfo('version'), '>=')) {
    $errors(
        esc_html__('Please install 5.1 or higher', LPTS_TEXT_DOMAIN),
        esc_html__('Incompatible WordPress version', LPTS_TEXT_DOMAIN)
    );
}
