<?php

/**
 * Form fields patial
 *
 * @package LPTS
 * @since   1.0.0
 */

?>

<?php if (empty($matched_attributes)): ?>
    <tr class="attributes_match_row">
        <td>
            <select name="lpts_woocommerce_customer_attributes[]" id="lpts_woocommerce_customer_attributes"
                    class="form-select">
                <?php foreach ($customer_fields['billing']['fields'] as $id => $label): ?>
                    <option value="<?php echo $id ? $id : ''; ?>">
                        <?php echo $label['label'] ? esc_attr($label['label']) : ''; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="text-center"><span class="dashicons dashicons-leftright"></span></td>
        <td>
            <select name="lpts_sendinblue_contact_attributes[]" id="lpts_sendinblue_contact_attributes"
                    class="form-select">
                <?php foreach ($contact_attributes as $attribute): ?>
                    <option value="<?= $attribute['name'] ? $attribute['name'] : ''; ?>">
                        <?php echo $attribute['name'] ? esc_attr($attribute['name']) : ''; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td></td>
    </tr>
<?php else: ?>
    <?php foreach ($matched_attributes as $k => $v): ?>
        <tr class="attributes_match_row">
            <td>
                <select name="lpts_woocommerce_customer_attributes[]" class="form-select"
                        id="lpts_woocommerce_customer_attributes">
                    <?php foreach ($customer_fields['billing']['fields'] as $id => $label): ?>
                        <option value="<?php echo $id ? $id : ''; ?>"
                            <?php if ($k == $id) {
                                foreach ($customer_attributes_option as $value) {
                                    selected($value, $id);
                                }
                            } ?>
                        >
                            <?php echo $label['label'] ? $label['label'] : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td class="text-center"><span class="dashicons dashicons-leftright"></span></td>
            <td>
                <select name="lpts_sendinblue_contact_attributes[]" id="lpts_sendinblue_contact_attributes"
                        class="form-select">
                    <?php foreach ($contact_attributes as $attribute): ?>
                        <option value="<?= $attribute['name'] ? $attribute['name'] : ''; ?>"
                            <?php if ($attribute['name'] == $v) {
                                foreach ($sendinblue_attributes_option as $value) {
                                    selected($value, $attribute['name']);
                                }
                            } ?>
                        >
                            <?php echo $attribute['name'] ? $attribute['name'] : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>