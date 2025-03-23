<?php

/**
 * Template for the variation product panel
 *
 * @var array $lists
 * @var array $saved
 * @var int $loop
 *
 * @since 1.2.0
 */

declare(strict_types=1);

?>

<div class="form-row form-row-full">
    <label for="variation_lpts_list_<?= $loop ?>">
        <?php _e('Brevo list', 'link-products-to-sendinblue'); ?>
    </label>

    <select
            name="variation_lpts_list[<?= $loop ?>][]"
            style="width: 100%;"
            aria-label="<?= esc_attr__('Select a list', 'link-products-to-sendinblue') ?>"
    >
        <?php foreach ($lists as $key => $label) : ?>
            <option value="<?= esc_attr($key) ?>" <?php selected(is_array($saved) && in_array($key, $saved, true)); ?>>
                <?= esc_html($label) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

