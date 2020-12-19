<?php
/**
 * Api key field partial
 *
 * @package WcProToSL
 * @since   1.0.5
 */

?>

<div class="input-group">
    <input class="form-control" placeholder="<?= __('xkeysib-....', WCPROTOSL_TEXT_DOMAIN) ?>" type="text"
           name="wcprotosl_api_key" size="105" value="<?= $api_key_v3 ?: ''; ?>"/>
</div>