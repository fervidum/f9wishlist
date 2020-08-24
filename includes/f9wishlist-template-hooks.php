<?php
/**
 * F9wishlist Template Hooks
 *
 * Action/filter hooks used for F9wishlist functions/templates.
 *
 * @package F9wishlist/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product Loop Items.
 *
 * @see f9wishlist_template_loop_add_to_wishlist()
 */
add_action( 'woocommerce_before_shop_loop_item', 'f9wishlist_template_loop_add_to_wishlist', 5 );
