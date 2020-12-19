<?php
/**
 * Form view
 *
 * @package WcProToSL
 * @since   1.0.0
 */

?>

<form action="options.php" method="post">
    <div class="container">
        <?php if (empty($options)): ?>
            <div class="row">
                <?php
                settings_fields($api_field_group);
                do_settings_sections($api_field_group);
                ?>
            </div>

            <input type="submit" name="submit" id="submit" class="btn btn-outline-primary mt-4"
                   value="<?= __('Save Changes', WCPROTOSL_TEXT_DOMAIN) ?>">
        <?php else: ?>
            <div class="row mt-3">
                <!--                --><?php //settings_fields($attributes_synch_group); ?>
                <!--                --><?php //do_settings_sections($attributes_synch_group); ?>

                <input type="submit" name="wcprotosl_delete_api_key" id="wcprotosl_delete_api_key"
                       class="btn btn-outline-primary mt-4" value="<?= __('Disconnect Sendinblue', WCPROTOSL_TEXT_DOMAIN) ?>">
            </div>
        <?php endif ?>
    </div>
</form>
