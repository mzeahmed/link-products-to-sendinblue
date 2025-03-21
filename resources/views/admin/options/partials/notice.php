<?php

/**
 * Empty api key notice view
 *
 * @package LPTS
 * @since   1.0.0
 */

?>

<div class="notice notice-error is-dismissible">
    <p>
		<?php
		printf(
			__(
				'To use Link Products To Sendinblue Lists From Woocommerce plugin, please enter your <a href="%s">API v3 Access key</a>.',
				'link-products-to-sendinblue'
			),
			admin_url( 'options-general.php?page=link_products_to_sendinblue' )
		);
		?>
    </p>
</div>