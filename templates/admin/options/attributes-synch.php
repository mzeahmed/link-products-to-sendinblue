<?php
/**
 * Attributes synch section
 *
 * @package WcProToSL
 * @since   1.0.5
 */

?>

<div class="mb-4">
    <?php _e(
        'Match the WooCommerce Customers attributes with your Sendinblue contacts attributes',
        WCPROTOSL_TEXT_DOMAIN
    ) ?>
</div>

<div class="col-md-5 pl-5">
    <div class="mb-2"><?php _e('Woocommerce Customers Attributes', WCPROTOSL_TEXT_DOMAIN); ?></div>
    <select name="" id="" class="form-select">
        <?php foreach ($customer_fields['billing']['fields'] as $id => $label): ?>
            <option value="<?php echo $id; ?>" <?php if ($id == $val) {
                echo 'selected';
            } ?>><?php echo $label['label']; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-md-5">
    <div class="mb-2"><?php _e('Sendinblue Contact Attributes', WCPROTOSL_TEXT_DOMAIN) ?></div>
    <select name="" id="" class="form-select">
        <?php foreach ($attrs as $attr): ?>
            <option value="<?php echo $attr['name']; ?>" <?php if ($attr['name'] == $key) {
                echo 'selected';
            } ?>><?php echo $attr['name']; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-md-2 mx-auto">
    <div class="mb-2"></div>
    <a href="javascript:void(0)" style="text-decoration: none;"
       id="wcprotosl-match-list-dismiss">
        <span class="dashicons dashicons-dismiss"></span>
    </a>
    <a href="javascript:void(0)" style="text-decoration: none;" id="wcprotosl-match-list-plus">
        <span class="dashicons dashicons-plus-alt "></span>
    </a>
</div>
