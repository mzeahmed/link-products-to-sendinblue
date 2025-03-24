<?php

/**
 * Product panel vieww
 *
 * @var array $lists
 * @var mixed $listIds
 * @var array $roles
 * @var bool $isVariable
 *
 * @package LPTS
 * @since   1.0.0
 *
 * @see \LPTS\Infrastructure\WordPress\Hook\Admin\Woocommerce\CustomProductPanelHook::productDataPanelRender()
 */

declare(strict_types=1);

if (!$isVariable) : ?>
    <div
            id="sendinblue_data_panel"
            class="panel woocommerce_options_panel hidden"
            data-lists="<?= esc_attr(json_encode($lists)) ?>"
            data-roles="<?= esc_attr(json_encode($roles)) ?>"
            data-is-variable="<?= esc_attr($isVariable) ?>"
    >
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
                    <?php if (empty($listIds)) :
                        $listIds = [[]];
                    endif;

                    foreach ($listIds as $index => $entry) :
                        $selectedList = $entry['list_id'] ?? '';
                        $condition = $entry['condition'] ?? 'always';
                        $param = $entry['param'] ?? '';
                        ?>

                        <tr>
                            <td class="list-cell">
                                <select
                                        name="_selec_list[<?= $index ?>][list_id]"
                                        class="wc-enhanced-select lpts-list"
                                        aria-label="<?= esc_attr__('Select a list', 'link-products-to-sendinblue') ?>"
                                >
                                    <?php foreach ($lists as $key => $label) : ?>
                                        <option value="<?= esc_attr($key) ?>" <?php selected($key, $selectedList); ?>>
                                            <?= esc_html($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td class="condition-cell">
                                <select
                                        name="_selec_list[<?= $index ?>][condition]"
                                        class="wc-enhanced-select lpts-condition"
                                        aria-label="<?= esc_attr__('Select a condition', 'link-products-to-sendinblue') ?>"
                                >
                                    <option value="always" <?php selected('always', $condition); ?>>
                                        <?= __('Always', 'link-products-to-sendinblue') ?>
                                    </option>
                                    <option value="order_total_gte" <?php selected('order_total_gte', $condition); ?>>
                                        <?= __('Order total >=', 'link-products-to-sendinblue') ?>
                                    </option>
                                    <option value="order_total_eq" <?php selected('order_total_eq', $condition); ?>>
                                        <?= __('Order total =', 'link-products-to-sendinblue') ?>
                                    </option>
                                    <option value="order_total_lt" <?php selected('order_total_lt', $condition); ?>>
                                        <?php _e('Order total <', 'link-products-to-sendinblue'); ?>
                                    </option>
                                    <!--<option value="user_role" --><?php //selected('user_role', $condition);
                                    ?><!-->-->
                                    <!--    --><?php //= __('User Role', 'link-products-to-sendinblue')
                                    ?>
                                    <!--</option>-->
                                </select>
                            </td>

                            <td class="param-cell">
                                <input
                                        type="text"
                                        name="_selec_list[<?= $index ?>][param]"
                                        value="<?= esc_attr($param) ?>"
                                        aria-label="<?= esc_attr__('Parameter', 'link-products-to-sendinblue') ?>"
                                >
                            </td>

                            <td>
                                <button type="button" class="button remove-row">
                                    <?= __('Remove', 'link-products-to-sendinblue') ?>
                                </button>
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
<?php else : ?>
    <div id="sendinblue_data_panel" class="panel woocommerce_options_panel notice notice-warning inline">
        <p>
            <?php _e(
                'Conditional logic is disabled for variable products. Please assign lists directly to each variation.',
                'link-products-to-sendinblue'
            ); ?>
        </p>
    </div>
<?php endif; ?>
