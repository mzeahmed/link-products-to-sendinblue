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
 * Version:           1.0.8
 * Author:            Ahmed Mze
 * Author URI:        https://ahmedmze.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-product-to-sendinblue-list
 * Domain Path:       /languages
 */

defined('ABSPATH') || die;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/constants.php';

/**
 * @return false | \WcProToSL\WoocommerceProductToSendinblueList
 * @since 1.0.8
 */
function woocommerce_product_to_sendinblue_list()
{
    if ( ! is_plugin_active('woocommerce/woocommerce.php')) {
        add_action('admin_notices', function () {
            return \WcProToSL\View\View::render('admin/woocommerce/dependency-notice', []);
        });
    }

    return \WcProToSL\WoocommerceProductToSendinblueList::get_instance();
}

woocommerce_product_to_sendinblue_list();
