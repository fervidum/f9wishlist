<?php

class_exists( 'WP_CLI' ) || exit;

class Pot_Command extends WP_CLI_Command {

	private $path;

	/**
	 * Create a POT file.
	 *
	 * @when before_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		$this->path = dirname( WP_CLI::get_runner()->get_project_config_path() );

		$pots = array();

		$theme = $this->theme();
		if ( $theme ) {
			$destination = $theme->path;
			if ( ! empty( $theme->domain_path ) ) {
				$destination .= "/{$theme->domain_path}";
			}
			$pots[] = (object) array(
				'source'      => $theme->path,
				'destination' => "{$destination}/{$theme->slug}.pot",
			);
		}
		$plugin = $this->plugin();
		if ( $plugin ) {
			$destination = $plugin->path;
			if ( ! empty( $plugin->domain_path ) ) {
				$destination .= "/{$plugin->domain_path}";
			}
			$pots[] = (object) array(
				'source'      => $plugin->path,
				'destination' => "{$destination}/{$plugin->slug}.pot",
			);
		}

		foreach ( $pots as $pot ) {
			WP_CLI::runcommand( "i18n make-pot {$pot->source} {$pot->destination}" );
		}
	}

	private function theme() {
		$paths = array_merge(
			array( $this->path ),
			$this->submodule_paths()
		);
		$theme = false;
		foreach ( $paths as $path ) {
			$style = current( glob( $path . '/style.css' ) );
			if ( ! empty( $style ) ) {
				$contents = file_get_contents( $style, false, null, 0, 5000 );
				if ( preg_match( '#[\s\*]*Text Domain:(.+)#', $contents, $matches ) ) {
					$theme = (object) array(
						'slug'        => trim( trim( $matches[1] ), '/' ),
						'path'        => $path,
						'domain_path' => 'languages',
					);
					if ( preg_match( '#[\s\*]*Domain Path:(.+)#', $contents, $matches ) ) {
						$theme->domain_path = trim( trim( $matches[1] ), '/' );
					}
					break;
				}
			}
		}
		return $theme;
	}

	private function plugin() {
		$paths  = array_merge(
			array( $this->path ),
			$this->submodule_paths()
		);
		$plugin = false;
		foreach ( $paths as $path ) {
			foreach ( glob( $path . '/*.php' ) as $php_file ) {
				$contents = file_get_contents( $php_file, false, null, 0, 5000 );
				if ( preg_match( '#[\s\*]*Plugin Name:#', $contents, $matches ) ) {
					$plugin = (object) array(
						'slug'        => preg_replace( '/\.php$/', '', basename( $php_file ) ),
						'path'        => $path,
						'domain_path' => 'languages',
					);
					if ( preg_match( '#[\s\*]*Domain Path:(.+)#', $contents, $matches ) ) {
						$plugin->domain_path = trim( trim( $matches[1] ), '/' );
					}
					break;
				}
			}
		}
		return $plugin;
	}

	private function submodule_paths() {
		$paths = array();
		if ( file_exists( '.gitmodules' ) ) {
			$gitmodules = parse_ini_file( '.gitmodules', true );
			foreach ( $gitmodules as $key => $submodule ) {
				$submodule = (object) $submodule;
				$paths[]   = realpath( $submodule->path );
			}
		}
		return $paths;
	}
}
WP_CLI::add_command( 'pot', 'Pot_Command' );
