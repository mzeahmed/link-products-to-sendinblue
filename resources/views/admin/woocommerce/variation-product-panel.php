<?php

/**
 * Template for the variation product panel
 *
 * @var array $lists
 * @var array $saved
 * @var int $loop
 * @var array $variationData
 *
 * @since 2.0.0
 */

declare(strict_types=1);

use LPTS\Shared\Enums\MetaKey;

?>

<div class="form-row form-row-full">
    <label for="<?= Metakey::VARIATION_PRODUCT_LISTS->value ?><?= $loop ?>">
        <?= __('Brevo list', 'link-products-to-sendinblue') ?>
    </label>

    <select
            name="<?= Metakey::VARIATION_PRODUCT_LISTS->value ?>[<?= $loop ?>]"
            style="width: 100%;"
            aria-label="<?= esc_attr__('Select a list', 'link-products-to-sendinblue') ?>"
    >
        <?php foreach ($lists as $key => $label) : ?>
            <option value="<?= esc_attr((string) $key) ?>" <?php selected((string) $saved === (string) $key); ?>>
                <?= esc_html($label) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

