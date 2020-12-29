<?php
/**
 * Dependency notice view
 *
 * @package WcProToSL
 * @since   1.0.8
 */

?>

<div class="notice notice-error is-dismissible">
    <p>
        <strong>
            <?php
            printf(
                esc_html__(
                    'WooCommerce Product To Sendinblue List requires WooCommerce to be installed and active. You can download %s here.',
                    WCPROTOSL_TEXT_DOMAIN
                ),
                '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>'
            );
            ?>

        </strong>
    </p>
</div>