<?php

class_exists( 'WP_CLI' ) || exit;

/**
 * Clean minify CSS.
 *
 * @when before_wp_load
 */
$cleancss_command = function( $args, $assoc_args ) {
	// Install 'cleancss' npm pack if not available.
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
	if ( ! in_array( 'clean-css-cli', $packages, true ) ) {
		WP_CLI::warning( "Package npm 'clean-css-cli' not installed" );
		WP_CLI::log( WP_CLI::colorize( "%GInstalling 'clean-css-cli'...%n%_" ) );
		WP_CLI::launch( 'sudo npm i -g clean-css-cli' );
	}

	passthru( 'cleancss --level 1 --format breakWith=lf --output assets/css/f9wishlist.min.css assets/css/f9wishlist.css' );
};
WP_CLI::add_command( 'cleancss', $cleancss_command );
