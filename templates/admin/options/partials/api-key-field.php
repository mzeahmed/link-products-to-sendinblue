<?php
/**
 * Api key field partial
 *
 * @package LPTS
 * @since   1.0.0
 */

?>

<div class="input-group">
    <input class="form-control" placeholder="<?= __('xkeysib-....', LPTS_TEXT_DOMAIN) ?>" type="text"
           name="lpts_api_key" size="105" value="<?= $api_key_v3 ?: ''; ?>" required/>
</div>