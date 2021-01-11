<?php
/**
 * Product panel vieww
 *
 * @package LPTS
 * @since   1.0.0
 */

?>

<div id="sendinblue_data_panel" class="panel woocommerce_options_panel hidden">
    <?php

    woocommerce_wp_select(
        [
            'id'            => '_selec_list',
            'label'         => __('List', LPTS_TEXT_DOMAIN),
            'wrapper_class' => 'show_if_simple',
            'options'       => $lists,
            'value'         => $value,
            'desc_tip'      => true,
            'description'   => __('The customer will be added to this list', LPTS_TEXT_DOMAIN),
        ]
    );

    ?>
</div>