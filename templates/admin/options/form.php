<?php
/**
 * Api key form view
 *
 * @package WcProToSL
 * @since   1.0.0
 */

?>

<?php $tab = isset($_GET['tab']) ? $_GET['tab'] : $api_field_group; ?>

<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a href="?page=woocommerce_product_to_sendinblue_list"
           class="nav-tab <?= $tab == $api_field_group ? 'nav-tab-active' : ''; ?>">
            <?php _e('API Key', WCPROTOSL_TEXT_DOMAIN); ?>
        </a>
        <a href="?page=woocommerce_product_to_sendinblue_list&tab=user_attributes"
           class="nav-tab <?= $tab == 'user_attributes' ? 'nav-tab-active' : ''; ?>">
            <?php _e('User attributes synch', WCPROTOSL_TEXT_DOMAIN); ?>
        </a>
    </h2>
    <form action="options.php" method="post">
        <?php if ($tab == 'user_attributes') : ?>
            <div class="mb-4 alert alert-info">
                <?php _e(
                    'Match WooCommerce Customers attributes with your Sendinblue contacts attributes',
                    WCPROTOSL_TEXT_DOMAIN
                ) ?>
            </div>
            <?php settings_fields($attributes_synch_group); ?>
            <?php do_settings_sections($attributes_synch_group); ?>

            <span id="write_root"></span>

            <div class="buttons_plus_less" style="float: right;margin-right: 50%;margin-top: -70px;">
                <a style="text-decoration: none;" href="#" id="user_attr_plus"
                   onclick="moreUserAttrSynchFields()">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e('Add more attributes', WCPROTOSL_TEXT_DOMAIN) ?>
                </a>
                <a style="display: none;  text-decoration: none; " href="#" id="user_attr_less"
                   onclick="lessUserAttrSynchFields()">
                    <span class="dashicons dashicons-no"></span>
                    <?php _e('Remove ', WCPROTOSL_TEXT_DOMAIN); ?>
                </a>
            </div>
        <?php else : ?>
            <?php settings_fields($api_field_group); ?>
            <?php do_settings_sections($api_field_group); ?>
        <?php endif; ?>

        <input type="submit" name="submit" id="submit" class="btn btn-outline-primary mt-4"
               value="<?= __('Save Changes', WCPROTOSL_TEXT_DOMAIN) ?>">
    </form>
</div>