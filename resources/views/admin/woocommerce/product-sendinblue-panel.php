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
 * @see \LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce\CustomProductPanelHook::productDataPanelRender()
 */

declare(strict_types=1);

?>

<div id="sendinblue_data_panel" class="panel woocommerce_options_panel hidden" data-lists='<?= esc_attr(json_encode($lists)) ?>'>
    <div id="lpts_list_wrapper">
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Brevo list', 'link-products-to-sendinblue'); ?></th>
                    <th><?php _e('Condition', 'link-products-to-sendinblue'); ?></th>
                    <th><?php _e('Parameter', 'link-products-to-sendinblue'); ?></th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="lpts_list_rows">
                <?php if (empty($value)) :
                    $value = [[]];
                endif;

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
                            <button type="button" class="button remove-row"><?= __('Remove', 'link-products-to-sendinblue') ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p>
            <button type="button" class="button" id="add_lpts_list_row">
                <?= __('Add List', 'link-products-to-sendinblue') ?>
            </button>
        </p>
    </div>
</div>

