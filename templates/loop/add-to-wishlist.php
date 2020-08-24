<?php
/**
 * Loop Add to Wishlist
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-wishlist.php.
 *
 * HOWEVER, on occasion F9wishlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package F9wishlist/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'f9wishlist_loop_add_to_wishlist_link',
	sprintf(
		'<div%s><a href="%s" %s>%s <span class="screen-reader-text">%s</span></a></div>',
		isset( $args['class'] ) ? ' class="' . esc_attr( implode( ' ', $args['class'] ) ) . '"' : '',
		esc_url( f9wishlist_product_add_to_wishlist_url() ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		'<svg width="22" height="22" viewBox="0 0 22 22"><path d="M12.089 20.146c3.842-3.29 7.018-6.277 8.303-8.43 1.406-2.353 1.378-5.145.455-6.99-.923-1.843-2.772-3.144-4.751-3.222-1.98-.078-3.843.963-5.096 2.73-1.175-1.656-2.885-2.675-4.726-2.731a5.358 5.358 0 00-.37 0c-1.98.079-3.828 1.38-4.75 3.224-.924 1.844-.952 4.636.454 6.99 1.285 2.152 4.46 5.14 8.304 8.43a1.852 1.852 0 002.177 0z"/></svg>',
		esc_html( f9wishlist_product_add_to_wishlist_text() )
	),
	$product,
	$args
);
