<?php
/**
 * F9wishlist Template
 *
 * Functions for the templating system.
 *
 * @package F9wishlist\Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'f9wishlist_template_loop_add_to_wishlist' ) ) {

	/**
	 * Get the add to wishlist template for the loop.
	 *
	 * @param array $args Arguments.
	 */
	function f9wishlist_template_loop_add_to_wishlist( $args = array() ) {
		global $product;

		if ( $product ) {
			$defaults = array(
				'class'      => array(
					'f9wishlist-add-to-wishlist',
					'add-to-wishlist',
				),
				'attributes' => array(
					'data-product_id'  => $product->get_id(),
					'data-product_sku' => $product->get_sku(),
					'aria-label'       => f9wishlist_product_add_to_wishlist_description(),
					'rel'              => 'nofollow',
				),
			);

			$args = apply_filters( 'f9wishlist_loop_add_to_wishlist_args', wp_parse_args( $args, $defaults ), $product );

			if ( isset( $args['attributes']['aria-label'] ) ) {
				$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
			}

			f9wishlist_get_template( 'loop/add-to-wishlist.php', $args );
		}
	}
}
