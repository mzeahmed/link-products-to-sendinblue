<?php
/**
 * Attributes synch section
 *
 * @package WcProToSL
 * @since   1.0.5
 */

?>

<td class="wcprotosl-match-attr" style="padding-top: 0px;">
    <div class="wcprotosl-match-attr-desc">
        <?php _e(
            'Match the WooCommerce Customers attributes with your Sendinblue contacts attributes',
            WCPROTOSL_TEXT_DOMAIN
        ) ?>
    </div>
    <table class="form-wcprotosl-table" id="wcprotosl-match-attribute-table">

        <tr>
            <td>
                <label for=""><?php _e('Woocommerce Customers Attributes', WCPROTOSL_TEXT_DOMAIN); ?></label>
            </td>
            <td></td>
            <td>
                <label for=""><?php _e('Sendinblue Contact Attributes', WCPROTOSL_TEXT_DOMAIN) ?></label>
            </td>
            <td></td>
        </tr>
        <tr class="wcprotosl-match-row">
            <td>
                <select class="wcprotosl-match-list-wp-attr" name="" style="width: 100%;">
                    <?php foreach ($customer_fields['billing']['fields'] as $id => $label): ?>
                        <option value="<?php echo $id; ?>" <?php if ($id == $val) {
                            echo 'selected="selected"';
                        } ?>><?php echo $label['label']; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td style="text-align: center;">
                <span class="dashicons dashicons-leftright"></span>
            </td>
            <td>
                <select class="wcprotosl-match-list-sib-attr" name="" style="width: 100%;">
                    <?php foreach ($attrs as $attr): ?>
                        <option value="<?php echo $attr['name']; ?>" <?php if ($attr['name'] == $key) {
                            echo 'selected="selected"';
                        } ?>><?php echo $attr['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <a href="javascript:void(0)" class="wcprotosl-match-list-dismiss"><span
                            class="dashicons dashicons-dismiss"></span></a>
            </td>
            <td style="border: none;">
                <a href="javascript:void(0)" class="wcprotosl-match-list-plus" style="display: none;"><span
                            class="dashicons dashicons-plus-alt "></span></a>
            </td>
            <input type="hidden" name="wcprotosl_matched_lists[]" class="wcprotosl-matched-lists">
        </tr>
    </table>
</td>