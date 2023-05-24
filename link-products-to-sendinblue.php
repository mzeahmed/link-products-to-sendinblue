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
 * Version:           1.1.5
 * Author:            Ahmed Mze
 * Author URI:        https://github.com/mzeahmed
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       link-products-to-sendinblue
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || die;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/version-compare.php';
require_once __DIR__ . '/constants.php';

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Let's retrieve plugin's datas
 *
 * @var $plugin_data
 */
$plugin_data = get_plugin_data( __FILE__ );

define( 'LPTS_PLUGIN_BASENAME', 'link-products-to-sendinblue/link-products-to-sendinblue.php' );
define( 'LPTS_VERSION', $plugin_data['Version'] );
define( 'LPTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'LPTS_URL', plugin_dir_url( __FILE__ ) );
define( 'LPTS_PLUGIN_NAME', $plugin_data['Name'] );
define( 'LPTS_TEXT_DOMAIN', $plugin_data['TextDomain'] );

/**
 * Plugin entry point Process
 *
 * @return LPTS\LPTS|null
 * @since 1.0.0
 */
function link_products_to_sendinblue(): ?LPTS\LPTS {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', static function () {
			return LPTS\View\View::render( 'admin/woocommerce/dependency-notice', array() );
		} );
	}

	return LPTS\LPTS::get_instance();
}

add_action( 'plugins_loaded', 'link_products_to_sendinblue' );
