<?php
/**
 * Sendinblue attributes view
 *
 * @package WcProToSL
 * @since   1.0.7
 */

?>

<?php _e('Sendinblue Contact Attributes', WCPROTOSL_TEXT_DOMAIN) ?>
<select name="wcprotosl_sendinblue_contact_attributes[]" id="wcprotosl_sendinblue_contact_attributes"
        class="form-select">
    <?php foreach ($contact_attributes as $attribute): ?>
        <option value="<?= $attribute['name'] ? $attribute['name'] : ''; ?>"
            <?php foreach ($option_values as $value) {
                selected($value, $attribute['name']);
            } ?>
        >
            <?php echo $attribute['name'] ? $attribute['name'] : ''; ?>
        </option>
    <?php endforeach; ?>
</select>