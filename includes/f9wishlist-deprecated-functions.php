<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @category Core
 * @package  F9wishlist\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Wrapper for _doing_it_wrong().
 *
 * @since 1.0.0
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 */
function f9wishlist_doing_it_wrong( $function, $message, $version ) {
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary

	if ( is_ajax() || f9wishlist()->is_rest_api_request() ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	} else {
		_doing_it_wrong( $function, $message, $version ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
