<?php
/**
 * Product panel vieww
 *
 * @var array $lists
 * @var mixed $value
 *
 * @package LPTS
 * @since   1.0.0
 *
 * @see \LPTS\Admin\Woocommerce\CustomProductField::productDataPanelRender()
 */

?>

<!--<div id="sendinblue_data_panel" class="panel woocommerce_options_panel hidden">-->
<!--    --><?php
//
//     // woocommerce_wp_select([
//     //     'id' => '_selec_list',
//     //     'label' => __('List', 'link-products-to-sendinblue'),
//     //     'wrapper_class' => 'show_if_simple',
//     //     'options' => $lists,
//     //     'value' => $value,
//     //     'desc_tip' => true,
//     //     'description' => __('The customer will be added to this list', 'link-products-to-sendinblue'),
//     // ]);
//
//     ?>
<!---->
<!--    <p class="form-field">-->
<!--        <label for="_selec_list[]">--><?php //_e('Listes Sendinblue', 'link-products-to-sendinblue'); ?><!--</label>-->
<!--        <select id="_selec_list" name="_selec_list[]" class="wc-enhanced-select" multiple style="width: 100%;">-->
<!--            --><?php //foreach ($lists as $key => $label) : ?>
<!--                <option value="--><?php //= esc_attr($key) ?><!--" --><?php //= (is_array($value) && in_array($key, $value, true)) ? 'selected' : '' ?><!-->-->
<!--                    --><?php //= esc_html($label) ?>
<!--                </option>-->
<!--            --><?php //endforeach; ?>
<!--        </select>-->
<!--        <span class="description">-->
<!--            --><?php //_e('Le client sera ajouté à toutes les listes sélectionnées.', 'link-products-to-sendinblue'); ?>
<!--        </span>-->
<!--    </p>-->
<!--</div>-->

<div id="sendinblue_data_panel" class="panel woocommerce_options_panel hidden">
    <div id="lpts_list_wrapper">
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Liste Sendinblue', 'link-products-to-sendinblue'); ?></th>
                    <th><?php _e('Condition', 'link-products-to-sendinblue'); ?></th>
                    <th><?php _e('Paramètre', 'link-products-to-sendinblue'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="lpts_list_rows">
                <?php
                if (empty($value)) {
                    $value = [[]];
                }
                foreach ($value as $entry) :
                    $selected_list = $entry['list_id'] ?? '';
                    $condition = $entry['condition'] ?? 'always';
                    $param = $entry['param'] ?? '';
                    ?>
                    <tr>
                        <td>
                            <select name="_selec_list[][list_id]" class="wc-enhanced-select">
                                <?php foreach ($lists as $key => $label) : ?>
                                    <option value="<?= esc_attr($key) ?>" <?php selected($key, $selected_list); ?>>
                                        <?= esc_html($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="_selec_list[][condition]">
                                <option value="always" <?php selected('always', $condition); ?>>
                                    <?= __('Always', 'link-products-to-sendinblue') ?>
                                </option>
                                <option value="order_total_gt" <?php selected('order_total_gt', $condition); ?>>
                                    <?= __('Order Total >', 'link-products-to-sendinblue') ?>
                                </option>
                                <option value="order_total_eq" <?php selected('order_total_eq', $condition); ?>>
                                    <?= __('Order Total =', 'link-products-to-sendinblue') ?>
                                </option>
                                <option value="user_role" <?php selected('user_role', $condition); ?>>
                                    <?= __('User Role', 'link-products-to-sendinblue') ?>
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="_selec_list[][param]" value="<?= esc_attr($param) ?>" />
                        </td>
                        <td>
                            <button type="button" class="button remove-row">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>
            <button type="button" class="button" id="add_lpts_list_row">Ajouter une liste</button>
        </p>
    </div>
</div>

<script>
    jQuery(function ($) {
      $('#add_lpts_list_row').on('click', function () {
        const row = `
                <tr>
                    <td>
                        <select name="_selec_list[][list_id]" class="wc-enhanced-select">
                            <?php foreach ($lists as $key => $label) : ?>
                                <option value="<?= esc_attr($key) ?>"><?= esc_html($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="_selec_list[][condition]">
                            <option value="always">Toujours</option>
                            <option value="order_total_gt">Montant ></option>
                            <option value="order_total_eq">Montant =</option>
                            <option value="user_role">Rôle utilisateur</option>
                        </select>
                    </td>

                    <td><input type="text" name="_selec_list[][param]" /></td>
                    <td><button type="button" class="button remove-row">Supprimer</button></td>
                </tr>
            `;
        $('#lpts_list_rows').append(row);
      });

      $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
      });
    });
</script>
