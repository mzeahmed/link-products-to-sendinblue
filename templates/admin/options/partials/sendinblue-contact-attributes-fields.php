<?php
/**
 * Sendinblue attributes view
 *
 * @package WcProToSL
 * @since   1.0.7
 */

use WcProToSL\Admin\WcProToSL_Settings;

?>

<?php _e('Sendinblue Contact Attributes', WCPROTOSL_TEXT_DOMAIN) ?>
<select name="wcprotosl_sendinblue_contact_attributes[]" id="wcprotosl_sendinblue_contact_attributes"
        class="form-select">
    <?php foreach ($attrs as $attr): ?>
        <option value="<?= $attr['name'] ? $attr['name'] : ''; ?>"
            <?php selected(get_option(WcProToSL_Settings::WCPROTOSL_SENDINBLUE_ATTRIBUTES_OPTION), $attr['name']) ?>
        >
            <?= $attr['name'] ? $attr['name'] : ''; ?>
        </option>
    <?php endforeach; ?>
</select>