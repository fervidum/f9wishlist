<?php
/**
 * F9wishlist F9wishlist_AJAX. AJAX Event Handlers.
 *
 * @class   F9wishlist_AJAX
 * @package F9wishlist\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * F9wishlist_Ajax class.
 */
class F9wishlist_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		$ajax_events_nopriv = array(
			'add_to_wishlist',
			'remove_from_wishlist',
		);

		foreach ( $ajax_events_nopriv as $ajax_event ) {
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			// WC AJAX can be used for frontend ajax requests.
			add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	/**
	 * AJAX add to wishlist.
	 */
	public static function add_to_wishlist() {
		ob_start();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'f9wishlist_add_to_wishlist_product_id', absint( $_POST['product_id'] ) );

		$wishlists = get_posts(
			array(
				'post_type' => 'wishlist',
				'author'    => get_current_user_id(),
			)
		);

		if ( empty( $wishlists ) ) {
			wp_insert_post(
				array(
					'post_type' => 'wishlist',
					'author'    => get_current_user_id(),
				)
			);
		}

		$wishlists = get_posts(
			array(
				'post_type' => 'wishlist',
				'author'    => get_current_user_id(),
			)
		);

		wp_send_json( $wishlists );
		// phpcs:enable
	}

	/**
	 * AJAX remove from wishlist.
	 */
	public static function remove_from_wishlist() {
		ob_start();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$wishlist_item_key = wc_clean( isset( $_POST['wishlist_item_key'] ) ? wp_unslash( $_POST['wishlist_item_key'] ) : '' );

		if ( $wishlist_item_key && false !== WC()->wishlist->remove_wishlist_item( $wishlist_item_key ) ) {
			self::get_refreshed_fragments();
		} else {
			wp_send_json_error();
		}
	}
}

F9wishlist_AJAX::init();
