<form action="options.php" method="post">
    <?php
    settings_fields($group);
    do_settings_sections($group);
    submit_button();
    ?>
</form>