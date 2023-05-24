<?php

declare( strict_types=1 );

namespace LPTS\View;

/**
 * Class View
 *
 * @package LPTS\View
 * @since   1.0.0
 */
class View {
	/**
	 * Allows to render a view
	 *
	 * @param string     $template
	 * @param array|null $data
	 *
	 * @return string|null
	 */
	public static function render( string $template, array $data = null ): ?string {
		( $data ) ? extract( $data ) : null;

		$path = self::getTemplatePath() . $template . '.php';

		if ( $path ) {
			ob_start();
			require( $path );

			return ob_get_contents();
		}

		return false;
	}

	/**
	 * Get the path of the template directory
	 *
	 * @return string|null
	 */
	private static function getTemplatePath(): ?string {
		return LPTS_PATH . 'templates' . DIRECTORY_SEPARATOR;
	}
}
