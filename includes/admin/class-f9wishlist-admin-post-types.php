<?php
/**
 * Post Types Admin
 *
 * @package  F9wishlist/admin
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'F9wishlist_Admin_Post_Types', false ) ) {
	new F9wishlist_Admin_Post_Types();
	return;
}

/**
 * F9wishlist_Admin_Post_Types Class.
 *
 * Handles the edit posts views and some functionality on the edit post screen for F9wishlist post types.
 */
class F9wishlist_Admin_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Add a post display state for special F9wishlist pages.
		add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
	}

	/**
	 * Add a post display state for special F9wishlist pages in the page list table.
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 */
	public function add_display_post_states( $post_states, $post ) {
		if ( f9wishlist_get_page_id( 'wishlist' ) === $post->ID ) {
			$post_states['f9wishlist_page_for_wishlist'] = __( 'Wishlist Page', 'f9wishlist' );
		}

		return $post_states;
	}
}

new F9wishlist_Admin_Post_Types();
