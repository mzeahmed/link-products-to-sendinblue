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
        <a href="?page=wc_product_to_sendinblue_list"
           class="nav-tab <?= $tab == $api_field_group ? 'nav-tab-active' : ''; ?>">
            <?php _e('API Key', WCPROTOSL_TEXT_DOMAIN); ?>
        </a>
        <a href="?page=wc_product_to_sendinblue_list&tab=user_attributes"
           class="nav-tab <?= $tab == 'user_attributes' ? 'nav-tab-active' : ''; ?>">
            <?php _e('User attributes synch', WCPROTOSL_TEXT_DOMAIN); ?>
        </a>
    </h2>
    <form action="options.php" method="post">
        <?php if ($tab == 'user_attributes') : ?>
            <?php if ( ! current_user_can('manage_options')) wp_die(__('Unauthorized user', WCPROTOSL_TEXT_DOMAIN)); ?>
            <?php wp_nonce_field($nonce_action, '_user_attributes_nonce') ?>
            <div class="mb-4 alert alert-info">
                <?php _e(
                    'Match the WooCommerce Customers attributes with your Sendinblue contacts attributes',
                    WCPROTOSL_TEXT_DOMAIN
                ) ?>
            </div>

            <table class="form-table table table-striped" id="attributes_synch_table">
                <thead>
                    <tr>
                        <th class="text-center">
                            <?php _e('Woocommerce Customer Attributes', WCPROTOSL_TEXT_DOMAIN) ?>
                        </th>
                        <th></th>
                        <th class="text-center">
                            <?php _e('Sendinblue Contact Attributes', WCPROTOSL_TEXT_DOMAIN) ?>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="wcprotosl_user_attributes_fields">
                    <?php include 'partials/form-fields.php' ?>
                </tbody>
            </table>

            <div class="buttons_section">
                <button type="button" class="btn btn-outline-primary" id="userAttributesAdd" onclick="addFields()">
                    <?php _e('Add section', WCPROTOSL_TEXT_DOMAIN); ?>
                </button>
                <button type="button" class="btn btn-outline-danger" id="userAttributesDel" onclick="removeFields()">
                    <?php _e('Remove section', WCPROTOSL_TEXT_DOMAIN); ?>
                </button>
            </div>

        <?php else : ?>
            <?php settings_fields($api_field_group); ?>
            <?php do_settings_sections($api_field_group); ?>
        <?php endif; ?>

        <input type="submit" name="submit" id="submit" class="btn btn-outline-primary mt-4"
               value="<?= __('Save Changes', WCPROTOSL_TEXT_DOMAIN) ?>">

    </form>
</div>