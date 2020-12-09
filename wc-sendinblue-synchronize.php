<?php

/**
 * @link              https://github.com/mzeahmed/wc-sendinblue-synchronize
 * @since             1.0.0
 * @package           Wc_Sendinblue_Synchronize
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Sendinblue Synchronize
 * Plugin URI:        https://github.com/mzeahmed/wc-sendinblue-synchronize
 * Description:       Synchronize customer to a specific Sendinblue list after payment
 * Version:           1.0.0
 * Author:            Ahmed Mze
 * Author URI:        https://ahmedmze.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-sendinblue-synchronize
 * Domain Path:       /languages
 */

// Exit if accessed directly
use Wc_Sendinblue_Synchronize\App;

defined('ABSPATH') || die;

if ( ! function_exists('get_plugin_data')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Get plugin data
 *
 * @var $plugin_data
 */
$plugin_data = get_plugin_data(__FILE__);

define('WC_SS_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WC_SS_VERSION', $plugin_data['Version']);
define('WC_SS_PATH', plugin_dir_path(__FILE__));
define('WC_SS_URL', plugin_dir_url(__FILE__));
define('WC_SS_NAME', $plugin_data['Name']);
define('WC_SS_TEXT_DOMAIN', $plugin_data['TextDomain']);

/** composer */
require_once __DIR__ . '/vendor/autoload.php';

new App();
