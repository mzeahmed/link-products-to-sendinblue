<?php

/**
 * Api key form view
 *
 * @package LPTS
 * @since   1.0.0
 */

?>

<?php $tab = $_GET['tab'] ?? $api_field_group; ?>

<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a href="?page=link_products_to_sendinblue"
           class="nav-tab <?php echo $tab === $api_field_group ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'API Key', 'link-products-to-sendinblue' ); ?>
        </a>
		<?php if ( ! empty( get_option( LPTS_API_KEY_V3_OPTION ) ) ) : ?>
            <a href="?page=link_products_to_sendinblue&tab=user_attributes"
               class="nav-tab <?php echo $tab === 'user_attributes' ? 'nav-tab-active' : ''; ?>">
				<?php _e( 'User attributes sync', 'link-products-to-sendinblue' ); ?>
            </a>
		<?php endif; ?>
    </h2>
    <form action="options.php" method="post">
		<?php if ( $tab === 'user_attributes' ) : ?>
			<?php if ( ! current_user_can( 'manage_options' ) ) :
				wp_die( esc_html__( 'Unauthorized user', 'link-products-to-sendinblue' ) );
			endif; ?>
			<?php wp_nonce_field( $nonce_action, '_user_attributes_nonce' ) ?>
            <div class="mb-4 alert alert-info">
				<?php esc_html_e(
					'Match the WooCommerce Customers attributes with your Brevo contacts attributes',
					'link-products-to-sendinblue'
				) ?>
            </div>

            <table class="form-table table table-striped" id="attributes_synch_table">
                <thead>
                <tr>
                    <th class="text-center">
						<?php esc_html_e( 'Woocommerce Customer Attributes', 'link-products-to-sendinblue' ) ?>
                    </th>
                    <th></th>
                    <th class="text-center">
						<?php esc_html_e( 'Brevo (ex Sendinblue) Contact Attributes', 'link-products-to-sendinblue' ) ?>
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="lpts_user_attributes_fields">
				<?php include 'partials/form-fields.php' ?>
                </tbody>
            </table>

            <div class="buttons_section">
                <button type="button" class="btn btn-outline-primary" id="userAttributesAdd" onclick="addFields()">
					<?php esc_html_e( 'Add section', 'link-products-to-sendinblue' ); ?>
                </button>
                <button type="button" class="btn btn-outline-danger" id="userAttributesDel" onclick="removeFields()">
					<?php esc_html_e( 'Remove section', 'link-products-to-sendinblue' ); ?>
                </button>
            </div>

		<?php else : ?>
			<?php settings_fields( $api_field_group ); ?>
			<?php do_settings_sections( $api_field_group ); ?>
		<?php endif; ?>

        <input
                type="submit"
                name="submit"
                id="submit"
                class="btn btn-outline-primary mt-4"
                value="<?php esc_html_e( 'Save Changes', 'link-products-to-sendinblue' ) ?>"
        >

    </form>
</div>