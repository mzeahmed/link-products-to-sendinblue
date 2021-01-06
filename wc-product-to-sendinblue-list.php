<?php

/**
 * @link              https://github.com/mzeahmed/wc-product-to-sendinblue-list
 * @since             1.0.0
 * @package           WcProToSL
 *
 * @wordpress-plugin
 * Plugin Name:       WC Product To Sendinblue List
 * Plugin URI:        https://github.com/mzeahmed/wc-product-to-sendinblue-list
 * Description:       Link WooCommerce product to a specific Sendinblue list to add the customer to that list
 * Version:           1.1.4
 * Author:            Ahmed Mze
 * Author URI:        https://ahmedmze.fr
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wc-product-to-sendinblue-list
 * Domain Path:       /languages
 */

defined('ABSPATH') || die;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/version-compare.php';

! function_exists('get_plugin_data') ? require_once ABSPATH . 'wp-admin/includes/plugin.php' : null;

/**
 * let's retrieve plugin's datas
 *
 * @var $plugin_data
 */
$plugin_data = get_plugin_data(__FILE__);

define(
    'WCPROTOSL_PLUGIN_BASENAME',
    'wc-product-to-sendinblue-list/wc-product-to-sendinblue-list.php'
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
 * @return false | \WcProToSL\WcProductToSendinblueList
 * @since 1.0.8
 */
function wc_product_to_sendinblue_list()
{
    if ( ! is_plugin_active('woocommerce/woocommerce.php')) {
        add_action(
            'admin_notices',
            function () {
                return \WcProToSL\View\View::render('admin/woocommerce/dependency-notice', []);
            }
        );
    }

    return \WcProToSL\WcProductToSendinblueList::get_instance();
}

wc_product_to_sendinblue_list();
