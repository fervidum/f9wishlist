<?php

class_exists( 'WP_CLI' ) || exit;

/**
 * Compile Sass to CSS.
 *
 * # OPTIONS
 *
 * [--watch]
 * : Watch stylesheets and recompile when they change.
 *
 * @when before_wp_load
 */
$sass_command = function( $args, $assoc_args ) {
	$home_dir = WP_CLI\Utils\get_home_dir();

	// Install 'sass' npm pack if not available.
	$packages = WP_CLI::launch( 'npm list -g --depth 0', false, true );
	if ( empty( $packages->stderr ) ) {
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
		if ( ! in_array( 'sass', $packages, true ) ) {
			WP_CLI::warning( "Package npm 'sass' not installed" );
			WP_CLI::log( WP_CLI::colorize( "%GInstalling 'sass'...%n%_" ) );
			WP_CLI::launch( 'sudo npm i -g sass' );
		}
	}
	WP_CLI::debug( 'Check has vendor composer path with packages.' );
	if ( ! is_dir( 'vendor' ) ) {
		WP_CLI::debug( 'Identified composer not installed' );
		WP_CLI::log( WP_CLI::colorize( '%GInstalling composer packages...%n%_' ) );
		WP_CLI::launch( "HOME=$home_dir composer install" );
	}

	$watch = WP_CLI\Utils\get_flag_value( $assoc_args, 'watch' ) ? ' --watch' : '';
	$cmd   = "sass$watch --no-source-map assets/css";
	WP_CLI::debug( $cmd );
	passthru( $cmd );
};
WP_CLI::add_command( 'sass', $sass_command );
