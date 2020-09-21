<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @package F9wishlist\Classes\Wishlists
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post types Class.
 */
class F9wishlist_Post_Types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'wishlist' ) ) {
			return;
		}

		do_action( 'f9wishlist_register_post_type' );

		register_post_type(
			'wishlist',
			apply_filters(
				'f9wishlist_register_post_type_wishlist',
				array(
					'labels'              => array(
						'name'          => __( 'Wishlists', 'f9wishlist' ),
						'singular_name' => __( 'Wishlist', 'f9wishlist' ),
						'all_items'     => __( 'All Wishlists', 'f9wishlist' ),
					),
					'public'  => true,
					'show_ui' => false,
				)
			)
		);

		do_action( 'f9wishlist_after_register_post_type' );
	}
}

F9wishlist_Post_types::init();
