<?php

class_exists( 'WP_CLI' ) || exit;

/**
 * Autoprefixer CSS.
 *
 * @when before_wp_load
 */
$postcss_command = function( $args, $assoc_args ) {
	// Install 'postcss' npm pack if not available.
	$packages = WP_CLI::launch( 'npm list -g --depth 0', false, true );
	$packages = $packages->stdout;
	preg_match_all( '/[+`]--\s+(.+)/', $packages, $output );
	if ( 2 === count( $output ) ) {
		$packages = $output[1];
		foreach ( $packages as &$package ) {
			$package = preg_replace( '/@[\.\d]+$/', '', $package );
		}
	} else {
		$packages = array();
	}
	if ( ! in_array( 'postcss-cli', $packages, true ) ) {
		WP_CLI::warning( "Package npm 'postcss-cli' not installed" );
		WP_CLI::log( WP_CLI::colorize( "%GInstalling 'postcss-cli'...%n%_" ) );
		WP_CLI::launch( 'sudo npm i -g postcss-cli' );
	}

	passthru( 'postcss assets/css/f9wishlist.css --output assets/css/f9wishlist.css' );
};
WP_CLI::add_command( 'postcss', $postcss_command );
