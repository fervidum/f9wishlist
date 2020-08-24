<?php
/**
 * Storefront support.
 *
 * @since   1.0.0
 * @package F9wishlist/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * F9wishlist_Storefront class.
 */
class F9wishlist_Storefront {

	/**
	 * Theme init.
	 */
	public static function init() {
		add_filter( 'storefront_customizer_css', array( __CLASS__, 'customizer_css' ) );
	}

	/**
	 * Customizer css.
	 *
	 * @param  string $styles Styles.
	 * @return string
	 */
	public static function customizer_css( $styles ) {
		$storefront_customizer = require get_theme_file_path( 'inc/customizer/class-storefront-customizer.php' );
		$storefront_theme_mods = $storefront_customizer->get_storefront_theme_mods();

		$styles .= '
		a svg {
			fill: ' . $storefront_theme_mods['accent_color'] . ';
		}';
		return $styles;
	}
}

F9wishlist_Storefront::init();
