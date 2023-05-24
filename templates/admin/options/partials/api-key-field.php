<?php
/**
 * Api key field partial
 *
 * @package LPTS
 * @since   1.0.0
 */

?>

<div class="input-group">
    <input
            class="form-control"
            placeholder="<?php esc_attr_e( 'xkeysib-....', 'link-products-to-sendinblue' ) ?>"
            type="text"
            name="lpts_api_key"
            size="105"
            value="<?php echo esc_attr( $api_key_v3 ) ?: ''; ?>"
            required
    />
</div>