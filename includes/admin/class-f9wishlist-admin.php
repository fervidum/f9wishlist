<?php
/**
 * F9wishlist Admin
 *
 * @class    F9wishlist_Admin
 * @package  F9wishlist/Admin
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * F9wishlist_Admin class.
 */
class F9wishlist_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'selfd_register', array( $this, 'register_selfdirectory' ) );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		// Self Directory.
		$filename = F9WISHLIST_ABSPATH . 'includes/libs/selfd/class-selfdirectory.php';
		if ( file_exists( $filename ) ) {
			include_once $filename;
		}

		include_once dirname( __FILE__ ) . '/class-f9wishlist-admin-post-types.php';
	}

	/**
	 * Use Selfd to updates.
	 */
	public function register_selfdirectory() {
		selfd( F9WISHLIST_PLUGIN_FILE );
	}
}

return new F9wishlist_Admin();
