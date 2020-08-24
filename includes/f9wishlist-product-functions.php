<?php
/**
 * F9wishlist Products
 *
 * Functions for handling terms/term meta.
 *
 * @package F9wishlist/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get url add to wishlist used mainly in loops.
 *
 * @return string
 */
function f9wishlist_product_add_to_wishlist_url() {
	global $product;
	$url = remove_query_arg(
		'added-to-wishlist',
		add_query_arg(
			array(
				'add-to-wishlist' => $product->get_id(),
			),
			( function_exists( 'is_feed' ) && is_feed() ) || ( function_exists( 'is_404' ) && is_404() ) ? $product->get_permalink() : ''
		)
	);
	return apply_filters( 'f9wishlist_product_add_to_f9wishlist_url', $url, $product );
}

/**
 * Get the add to wishlist button text.
 *
 * @return string
 */
function f9wishlist_product_add_to_wishlist_text() {
	global $product;
	return apply_filters( 'f9wishlist_product_add_to_wishlist_text', __( 'Add to wishlist', 'f9wishlist' ), $product );
}

/**
 * Get the add to wishlist button text description - used in aria tags.
 *
 * @since 1.0.0
 * @return string
 */
function f9wishlist_product_add_to_wishlist_description() {
	global $product;
	/* translators: %s: Product title */
	return apply_filters( 'f9wishlist_product_add_to_wishlist_description', sprintf( __( 'Add &ldquo;%s&rdquo; to wishlist', 'f9wishlist' ), $product->get_name() ), $product );
}
