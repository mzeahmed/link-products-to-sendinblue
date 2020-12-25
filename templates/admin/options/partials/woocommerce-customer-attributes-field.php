<?php

/**
 * Custommer attributes view
 *
 * @package WcProToSL
 * @since   1.0.7
 */

use WcProToSL\Admin\WcProToSL_Settings;

?>

<?php _e('Woocommerce Customer Attributes', WCPROTOSL_TEXT_DOMAIN) ?>
<select name="wcprotosl_woocommerce_customer_attributes[]" id="wcprotosl_woocommerce_customer_attributes"
        class="form-select">
    <?php foreach ($customer_fields['billing']['fields'] as $id => $label): ?>
        <option value="<?= $id ? $id : ''; ?>"
            <?php selected(get_option(WcProToSL_Settings::WCPROTOSL_CUSTOMER_ATTRIBUTES_OPTION), $id) ?>
        >
            <?= $label['label'] ? $label['label'] : ''; ?>
        </option>
    <?php endforeach; ?>
</select>