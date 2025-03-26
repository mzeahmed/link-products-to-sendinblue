<?php

/**
 * Form fields patial
 *
 * @var array $customer_fields
 * @var array $contact_attributes
 * @var array $customer_attributes_option
 * @var array $sendinblue_attributes_option
 *
 * @package LPTS
 * @since   1.0.0
 */

?>

<?php if (empty($matched_attributes)) : ?>
    <tr class="attributes_match_row">
        <td>
            <select
                    name="lpts_woocommerce_customer_attributes[]"
                    id="lpts_woocommerce_customer_attributes"
                    class="form-select"
                    aria-label="<?= esc_attr__('Select WooCommerce customer attribute', 'link-products-to-sendinblue') ?>"
            >
                <?php foreach ($customer_fields['billing']['fields'] as $id => $label) : ?>
                    <option value="<?= $id ?: '' ?>">
                        <?= $label['label'] ? esc_html($label['label']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td class="text-center"><span class="dashicons dashicons-leftright"></span></td>

        <td>
            <select
                    name="lpts_sendinblue_contact_attributes[]"
                    id="lpts_sendinblue_contact_attributes"
                    class="form-select"
                    aria-label="<?= esc_attr__('Select Brevo contact attribute', 'link-products-to-sendinblue') ?>"
            >
                <?php foreach ($contact_attributes as $attribute) : ?>
                    <option value="<?= esc_attr($attribute['name']) ?>">
                        <?= $attribute['name'] ? esc_html($attribute['name']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td></td>
    </tr>
<?php else : ?>
    <?php foreach ($matched_attributes as $k => $v) : ?>
        <tr class="attributes_match_row">
            <td>
                <select
                        name="lpts_woocommerce_customer_attributes[]"
                        class="form-select"
                        id="lpts_woocommerce_customer_attributes"
                        aria-label="<?= esc_attr__('Select WooCommerce customer attribute', 'link-products-to-sendinblue') ?>"
                >
                    <?php foreach ($customer_fields['billing']['fields'] as $id => $label) : ?>
                        <option value="<?= esc_attr($id) ?>"
                            <?php if ($k === $id) :
                                foreach ($customer_attributes_option as $value) :
                                    selected($value, $id);
                                endforeach;
                            endif; ?>
                        >
                            <?= esc_html($label['label'] ?: '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td class="text-center"><span class="dashicons dashicons-leftright"></span></td>

            <td>
                <select
                        name="lpts_sendinblue_contact_attributes[]"
                        id="lpts_sendinblue_contact_attributes"
                        class="form-select"
                        aria-label="<?= esc_attr__('Select Brevo contact attribute', 'link-products-to-sendinblue') ?>"
                >
                    <?php foreach ($contact_attributes as $attribute) : ?>
                        <option value="<?= esc_attr($attribute['name']) ?>"
                            <?php if ($attribute['name'] === $v) :
                                foreach ($sendinblue_attributes_option as $value) :
                                    selected($value, $attribute['name']);
                                endforeach;
                            endif; ?>
                        >
                            <?= esc_html($attribute['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>