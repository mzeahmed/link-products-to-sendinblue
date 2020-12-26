<?php
/**
 * Custommer attributes view
 *
 * @package WcProToSL
 * @since   1.0.7
 */

?>

<?php _e('Woocommerce Customer Attributes', WCPROTOSL_TEXT_DOMAIN) ?>
<select name="wcprotosl_woocommerce_customer_attributes[]" id="wcprotosl_woocommerce_customer_attributes"
        class="form-select">
    <?php foreach ($customer_fields['billing']['fields'] as $id => $label): ?>
        <option value="<?php echo $id ? $id : ''; ?>"
            <?php foreach ($option_values as $value) {
                selected($value, $id);
            } ?>
        >
            <?php echo $label['label'] ? $label['label'] : ''; ?>
        </option>
    <?php endforeach; ?>
</select>