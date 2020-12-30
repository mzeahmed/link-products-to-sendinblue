<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

! function_exists('get_plugin_data') ? require_once ABSPATH . 'wp-admin/includes/plugin.php' : null;

$plugin_data = get_plugin_data(__FILE__);

define('WCPROTOSL_PLUGIN_BASENAME', 'woocommerce-product-to-sendinblue-list/woocommerce-product-to-sendinblue-list.php');
define('WCPROTOSL_VERSION', $plugin_data['Version']);
define('WCPROTOSL_PATH', plugin_dir_path(dirname(__FILE__)));
define('WCPROTOSL_URL', plugin_dir_url(dirname(__FILE__)));
define('WCPROTOSL_NAME', $plugin_data['Name']);
define('WCPROTOSL_TEXT_DOMAIN', $plugin_data['TextDomain']);

/*** Settings constants ***/
// main settings
define('WCPROTOSL_MAIN_OPTION_NAME', 'wcprotosl_main_option');

// api key option
define('WCPROTOSL_API_KEY_V3_OPTION', 'wcprotosl_api_key');

// attributes synch options
define('WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION', 'wcprotosl_woocommerce_customer_attributes');
define('WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION', 'wcprotosl_sendinblue_contact_attributes');
