<?php

class_exists( 'WP_CLI' ) || exit;

/**
 * Lint javascripts.
 *
 * [--fix]
 * : Fixes potentially fixable.
 *
 * @when before_wp_load
 */
$es_lint_command = function( $args, $assoc_args ) {
	$fix = '';
	if ( WP_CLI\Utils\get_flag_value( $assoc_args, 'fix' ) ) {
		$fix = ' --fix';
	}
	passthru( "wp-scripts lint-js 'assets/js/frontend/f9wishlist.js'" . $fix );
};
WP_CLI::add_command( 'lint-js', $es_lint_command );
