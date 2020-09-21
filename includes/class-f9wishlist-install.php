<?php
/**
 * Installation related functions and actions.
 *
 * @package F9wishlist/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * F9wishlist_Install Class.
 */
class F9wishlist_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
	}

	/**
	 * Check F9wishlist version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'f9wishlist_version' ), f9wishlist()->version, '<' ) ) {
			self::install();
			do_action( 'f9wishlist_updated' );
		}
	}

	/**
	 * Install F9wishlist.
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'f9wishlist_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'f9wishlist_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		if ( ! defined( 'F9WISHLIST_INSTALLING' ) ) {
			define( 'F9WISHLIST_INSTALLING', true );
		}

		add_action( 'woocommerce_init', array( __CLASS__, 'create_pages' ) );
		self::update_f9wishlist_version();

		delete_transient( 'f9wishlist_installing' );
	}

	/**
	 * Update F9wishlist version to current.
	 */
	private static function update_f9wishlist_version() {
		delete_option( 'f9wishlist_version' );
		add_option( 'f9wishlist_version', f9wishlist()->version );
	}

	/**
	 * Create pages that the plugin relies on, storing page IDs in variables.
	 */
	public static function create_pages() {
		$admin_functions = WP_PLUGIN_DIR . '/woocommerce/includes/admin/wc-admin-functions.php';
		if ( ! file_exists( $admin_functions ) ) {
			return;
		} else {
			require $admin_functions;
		}

		f9wishlist()->load_plugin_textdomain();

		$pages = apply_filters(
			'f9wishlist_create_pages',
			array(
				'wishlist' => array(
					'name'    => _x( 'wishlist', 'Page slug', 'f9wishlist' ),
					'title'   => _x( 'Wishlist', 'Page title', 'f9wishlist' ),
					'content' => '',
				),
			)
		);

		foreach ( $pages as $key => $page ) {
			$option = 'f9wishlist_' . $key . '_page_id';
			if ( intval( get_option( $option, 0 ) ) > 0 ) {
				continue;
			}
			wc_create_page(
				esc_sql( $page['name'] ),
				$option, $page['title'],
				$page['content'],
				! empty( $page['parent'] ) ? wc_get_page_id( $page['parent'] ) : ''
			);
		}
	}
}

F9wishlist_Install::init();
