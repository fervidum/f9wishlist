<?php
/**
 * F9wishlist Page Functions
 *
 * Functions related to pages and menus.
 *
 * @package F9wishlist\Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retrieve page ids - used for wishlist. returns -1 if no page is found.
 *
 * @param string $page Page slug.
 * @return int
 */
function f9wishlist_get_page_id( $page ) {
	$page = apply_filters( 'f9wishlist_get_' . $page . '_page_id', get_option( 'f9wishlist_' . $page . '_page_id' ) );

	return $page ? absint( $page ) : -1;
}
