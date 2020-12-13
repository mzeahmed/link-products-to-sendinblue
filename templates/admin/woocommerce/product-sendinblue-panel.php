<div id="sendinblue_data_panel" class="panel woocommerce_options_panel hidden">
    <?php

    woocommerce_wp_select(
        [
            'id'            => '_selec_list',
            'label'         => __('Please select a list', WC_SS_PLUGIN_BASENAME),
            'wrapper_class' => 'show_if_simple',
            'options'       => $lists,
            'value'         => $value,
            'desc_tip'      => true,
            'description'   => __('The customer will be added to this list', WC_SS_PLUGIN_BASENAME),
        ]
    );

    ?>
</div>