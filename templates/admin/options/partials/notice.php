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
                'To activate WooCommerce Sendinblue Synchronize, please enter your API v3 Access key.',
                WCPROTOSL_TEXT_DOMAIN
            ),
            admin_url('options-general.php?page=woocommerce_product_to_sendinblue_list')
        );
        ?>
    </p>
</div>