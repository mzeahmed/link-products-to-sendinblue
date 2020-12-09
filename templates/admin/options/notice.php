<div class="notice notice-error is-dismissible">
    <p>
        <?php
        printf(
            __(
                'To activate WooCommerce Sendinblue Synchronize, please enter your API v3 Access key.',
                WC_SS_TEXT_DOMAIN
            ),
            admin_url('options-general.php?page=wc_sendinblue_synchronize')
        );
        ?>
    </p>
</div>