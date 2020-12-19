<?php
/**
 * Notice view
 *
 * @package WcProToSL
 * @since   1.0.0
 */

?>

<div class="notice notice-error is-dismissible">
    <p>
        <?php
        printf(
            __(
                'To use WooCommerce Product To Sendinblue List, please enter your <a href="%s">API v3 Access key</a>.',
                WCPROTOSL_TEXT_DOMAIN
            ),
            admin_url('options-general.php?page=woocommerce_product_to_sendinblue_list')
        );
        ?>
    </p>
</div>