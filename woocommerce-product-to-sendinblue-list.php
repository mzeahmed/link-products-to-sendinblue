<?php

/**
 * @link              https://github.com/mzeahmed/woocommerce-product-to-sendinblue-list
 * @since             1.0.0
 * @package           WcProToSL
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Product To Sendinblue List
 * Plugin URI:        https://github.com/mzeahmed/woocommerce-product-to-sendinblue-list
 * Description:       Link WooCommerce product to a specific Sendinblue list to add the customer to that list
 * Version:           1.1.3
 * Author:            Ahmed Mze
 * Author URI:        https://ahmedmze.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-product-to-sendinblue-list
 * Domain Path:       /languages
 */

defined('ABSPATH') || die;

require_once __DIR__ . '/vendor/autoload.php';

! function_exists('get_plugin_data') ? require_once ABSPATH . 'wp-admin/includes/plugin.php' : null;

$plugin_data = get_plugin_data(__FILE__);

define(
    'WCPROTOSL_PLUGIN_BASENAME',
    'woocommerce-product-to-sendinblue-list/woocommerce-product-to-sendinblue-list.php'
);
define('WCPROTOSL_VERSION', $plugin_data['Version']);
define('WCPROTOSL_PATH', plugin_dir_path(__FILE__));
define('WCPROTOSL_URL', plugin_dir_url(__FILE__));
define('WCPROTOSL_PLUGIN_NAME', $plugin_data['Name']);
define('WCPROTOSL_TEXT_DOMAIN', $plugin_data['TextDomain']);

/*** Settings constants ***/
// main settings
define('WCPROTOSL_MAIN_OPTION', 'wcprotosl_main_option');

// api key option
define('WCPROTOSL_API_KEY_V3_OPTION', 'wcprotosl_api_key');

// attributes synch options
define('WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION', 'wcprotosl_woocommerce_customer_attributes');
define('WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION', 'wcprotosl_sendinblue_contact_attributes');

/**
 * @return false | \WcProToSL\WoocommerceProductToSendinblueList
 * @since 1.0.8
 */
function woocommerce_product_to_sendinblue_list()
{
    if ( ! is_plugin_active('woocommerce/woocommerce.php')) {
        add_action(
            'admin_notices',
            function () {
                return \WcProToSL\View\View::render('admin/woocommerce/dependency-notice', []);
            }
        );
    }

    return \WcProToSL\WoocommerceProductToSendinblueList::get_instance();
}

woocommerce_product_to_sendinblue_list();
