<?php

class_exists( 'WP_CLI' ) || exit;

class Link_I18n_Command extends WP_CLI_Command {

	private $source;

	/**
	 * Link languages to content.
	 */
	public function __invoke( $args, $assoc_args ) {
		$this->source = dirname( WP_CLI::get_runner()->get_project_config_path() );
		if ( is_dir( "{$this->source}/plugins" ) ) {
			$this->link_plugins( "{$this->source}/plugins" );
		}

		$is_theme = false;

		$extension = $this->theme();
		if ( $extension ) {
			$type     = 'theme';
			$target   = WP_CONTENT_DIR . '/themes';
			$is_theme = true;
		} else {
			$type      = 'plugin';
			$extension = $this->plugin();
			$target    = WP_PLUGIN_DIR;
		}
		$path = $this->source . '/' . $extension->domain_path;
		if ( glob( $path . '/*.[pm]o' ) ) {
			foreach ( glob( $path . '/*.[pm]o' ) as $file ) {
				$basename = basename( $file );
				$file     = WP_CONTENT_DIR . "/languages/themes/{$extension->slug}-{$basename}";
				$this->link_file( "$path/$basename", $file );
			}
			WP_CLI::success( 'Linked languages.' );
		} else {
			WP_CLI::warning( 'Not found languages.' );
		}
	}

	private function rm( $path ) {
		if ( file_exists( $path ) ) {
			$cmd = 'rm -' . ( is_dir( $path ) ? 'r' : '' ) . 'f %s';
			passthru( WP_CLI\Utils\esc_cmd( $cmd, $path ) );
		}
	}

	private function link_file( $source, $target ) {
		if ( file_exists( $target ) ) {
			$this->rm( $target );
		}
		WP_CLI::debug( "Linked '$source' to '$target'" );
		passthru( WP_CLI\Utils\esc_cmd( 'ln -s %s %s', $source, $target ) );
	}

	private function link_plugins( $dir ) {
		foreach ( $this->scandir( $dir, true ) as $filename ) {
			if ( ! is_dir( "$dir/$filename" ) ) {
				continue;
			}
			$this->link_path( "$dir/$filename", WP_PLUGIN_DIR . "/$filename" );
			WP_CLI::success(
				WP_CLI\Utils\esc_cmd(
					'Linked plugin from %s to %s.',
					"$dir/$filename",
					WP_PLUGIN_DIR . "/$filename"
				)
			);
		}
	}

	private function theme() {
		$slug = false;
		if ( ! file_exists( $this->source . '/style.scss' ) && file_exists( $this->source . '/assets/scss/style.scss' ) ) {
			if ( is_array( WP_CLI::get_runner()->find_command_to_run( array( 'sass' ) ) ) ) {
				WP_CLI::runcommand( 'sass' );
			}
		}
		$style = current( glob( $this->source . '/style.css' ) );
		if ( ! empty( $style ) ) {
			$contents = file_get_contents( $style, false, null, 0, 5000 );
			if ( preg_match( '#[\s\*]*Text Domain:(.+)#', $contents, $matches ) ) {
				$slug        = trim( trim( $matches[1] ), '/' );
				$domain_path = 'languages';
				if ( preg_match( '#[\s\*]*Domain Path:(.+)#', $contents, $matches ) ) {
					$domain_path = trim( trim( $matches[1] ), '/' );
				}
			}
		}
		if ( $slug ) {
			$slug = (object) array(
				'slug'        => $slug,
				'domain_path' => $domain_path,
			);
		}
		return $slug;
	}

	private function plugin() {
		$slug = false;
		foreach ( glob( $this->source . '/*.php' ) as $php_file ) {
			$contents = file_get_contents( $php_file, false, null, 0, 5000 );
			if ( preg_match( '#[\s\*]*Plugin Name:#', $contents, $matches ) ) {
				$slug        = preg_replace( '/\.php$/', '', basename( $php_file ) );
				$domain_path = 'languages';
				if ( preg_match( '#[\s\*]*Domain Path:(.+)#', $contents, $matches ) ) {
					$domain_path = trim( trim( $matches[1] ), '/' );
				}
			}
		}
		if ( $slug ) {
			$slug = (object) array(
				'slug'        => $slug,
				'domain_path' => $domain_path,
			);
		}
		return $slug;
	}

	/**
	 * List files and directories inside the specified path
	 *
	 * @param  string     $directory        The directory that will be scanned.
	 * @param  boolean    $ignore_only_dots Ignore marks to current '.' and parent '..' directories.
	 * @return array|bool
	 */
	private function scandir( $directory, $ignore_only_dots = false ) {
		$contents = scandir( $directory );
		if ( true === $ignore_only_dots ) {
			$contents = array_values(
				array_diff(
					$contents,
					array( '.', '..' )
				)
			);
		}
		return $contents;
	}
};
WP_CLI::add_command( 'ln-i18n', 'Link_I18n_Command' );
