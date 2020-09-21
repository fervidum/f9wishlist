<?php
/**
 * Plugin Name: F9wishlist
 * Plugin URI: https://fervidum.github.io/f9wishlist/
 * Description: WooCommerce Wishlist for customers create, fill, manage their wishlists.
 * Version: 1.0.0-alpha
 * Author: Fervidum
 * Author URI: https://fervidum.github.io/
 * Text Domain: f9wishlist
 * Domain Path: /languages/
 * Requires at least: 5.2
 * Requires PHP: 7.0
 *
 * @package F9wishlist
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'F9WISHLIST_PLUGIN_FILE' ) ) {
	define( 'F9WISHLIST_PLUGIN_FILE', __FILE__ );
}

// Include the main F9wishlist class.
if ( ! class_exists( 'F9wishlist', false ) ) {
	include_once dirname( F9WISHLIST_PLUGIN_FILE ) . '/includes/class-f9wishlist.php';
}

/**
 * Returns the main instance of F9wishlist.
 *
 * @since  1.0.0
 * @return F9wishlist
 */
function f9wishlist() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return F9wishlist::instance();
}

f9wishlist();
