<?php

/**
 * @link              https://github.com/mzeahmed/link-products-to-sendinblue
 * @since             1.0.0
 * @package           LPTS
 *
 * @wordpress-plugin
 * Plugin Name:       Link Products To Sendinblue Lists From Woocommerce
 * Plugin URI:        https://wordpress.org/plugins/link-products-to-sendinblue/
 * Description:       Link WooCommerce products to a specific Brevo (ex Sendinblue) list to add the customer to that list
 * Version:           1.1.7.4
 * Author:            Ahmed Mze
 * Author URI:        https://github.com/mzeahmed
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       link-products-to-sendinblue
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/version-compare.php';
require_once __DIR__ . '/constants.php';

if (!function_exists('get_plugin_data')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

define('LPTS_PLUGIN_FILE', __FILE__);

/**
 * Let's retrieve plugin's datas
 *
 * @var $plugin_data
 */
$plugin_data = get_plugin_data(LPTS_PLUGIN_FILE);

define('LPTS_PLUGIN_BASENAME', plugin_basename(LPTS_PLUGIN_FILE));
define('LPTS_VERSION', $plugin_data['Version']);
define('LPTS_PATH', plugin_dir_path(LPTS_PLUGIN_FILE));
define('LPTS_URL', plugin_dir_url(LPTS_PLUGIN_FILE));
define('LPTS_PLUGIN_NAME', $plugin_data['Name']);
define('LPTS_TEXT_DOMAIN', $plugin_data['TextDomain']);

/**
 * Plugin entry point Process
 *
 * @return LPTS\Bootstrap|null
 * @since 1.0.0
 */
function link_products_to_sendinblue(): ?LPTS\Bootstrap
{
    if (!function_exists('WC')) {
        add_action('admin_notices', static function () {
            return \LPTS\Infrastructure\View\View::render('admin/woocommerce/dependency-notice', []);
        });
    }

    return LPTS\Bootstrap::getInstance();
}

add_action('plugins_loaded', 'link_products_to_sendinblue');
