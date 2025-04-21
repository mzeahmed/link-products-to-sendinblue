<?php

declare(strict_types=1);

/**
 * @link              https://github.com/mzeahmed/link-products-to-sendinblue
 * @since             1.0.0
 * @package           LPTS
 *
 * @wordpress-plugin
 * Plugin Name:       Link Products To Sendinblue Lists From Woocommerce
 * Plugin URI:        https://wordpress.org/plugins/link-products-to-sendinblue/
 * Description:       Link WooCommerce products to a specific Brevo (ex Sendinblue) list to add the customer to that list
 * Version:           2.0.4
 * Requires Plugins:  woocommerce
 * Author:            mzeahmed
 * Author URI:        https://github.com/mzeahmed
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       link-products-to-sendinblue
 * Domain Path:       /resources/i18n
 */

use LPTS\Bootstrap;
use LPTS\Shared\Utils\Utils;

if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

define('LPTS_PLUGIN_FILE', __FILE__);

/**
 * Let's retrieve plugin's datas
 *
 * @var $plugin_data
 */
$plugin_data = get_plugin_data(LPTS_PLUGIN_FILE);

define('LPTS_PLUGIN_BASENAME', plugin_basename(LPTS_PLUGIN_FILE));
define('LPTS_PLUGIN_PATH', plugin_dir_path(LPTS_PLUGIN_FILE));
define('LPTS_PLUGIN_URL', plugin_dir_url(LPTS_PLUGIN_FILE));

define('LPTS_CURRENT_DB_VERSION', '1.0.1');

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Plugin entry point Process
 *
 * @return LPTS\Bootstrap|null
 * @since 1.0.0
 */
function link_products_to_sendinblue(): ?Bootstrap
{
    Utils::versionCompare();

    return Bootstrap::getInstance();
}

link_products_to_sendinblue();
