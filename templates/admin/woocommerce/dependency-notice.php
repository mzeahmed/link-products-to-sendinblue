<?php
/**
 * Dependency notice view
 *
 * @package LPTS
 * @since   1.0.8
 */

?>

<div class="notice notice-error is-dismissible">
    <p>
        <strong>
            <?php
            printf(
                __(
                    'Link Products To Sendinblue Lists From Woocommerce plugin requires WooCommerce to be installed and active. You can download %s here.',
                    LPTS_TEXT_DOMAIN
                ),
                '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>'
            );
            ?>

        </strong>
    </p>
</div>