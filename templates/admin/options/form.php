<?php
/**
 * Form view
 *
 * @package WcProToSL
 * @since   1.0.0
 */

?>

<form action="options.php" method="post">
    <?php
    settings_fields($api_field_group);
    do_settings_sections($api_field_group);

    if ( ! empty($options)) {
        settings_fields($attributes_synch_group);
        do_settings_sections($attributes_synch_group);
    }

    submit_button();
    ?>
</form>
