<?php

/**
 * @link              https://github.com/mzeahmed/woocommerce-product-to-sendinblue-list
 * @since             1.0.0
 * @package           WcProToSL
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Product To Sendinblue List
 * Plugin URI:        https://github.com/mzeahmed/woocommerce-product-to-sendinblue-list
 * Description:       Link a WooCommerce product to a specific Sendinblue list to add the customer to that list
 * Version:           1.0.7
 * Author:            Ahmed Mze
 * Author URI:        https://ahmedmze.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-product-to-sendinblue-list
 * Domain Path:       /languages
 */

defined('ABSPATH') || die;

if ( !function_exists('get_plugin_data')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$plugin_data = get_plugin_data(__FILE__);

define('WCPROTOSL_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WCPROTOSL_VERSION', $plugin_data['Version']);
define('WCPROTOSL_PATH', plugin_dir_path(__FILE__));
define('WCPROTOSL_URL', plugin_dir_url(__FILE__));
define('WCPROTOSL_NAME', $plugin_data['Name']);
define('WCPROTOSL_TEXT_DOMAIN', $plugin_data['TextDomain']);

require_once __DIR__ . '/vendor/autoload.php';

function wcprotosl()
{
    new WcProToSL\App();
}

wcprotosl();
