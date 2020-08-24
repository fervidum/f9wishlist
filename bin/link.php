<?php

class_exists( 'WP_CLI' ) || exit;

class Link_Command extends WP_CLI_Command {

	private $source;

	/**
	 * Link wp-content content.
	 *
	 * @alias link
	 */
	public function __invoke( $args, $assoc_args ) {
		$this->source = dirname( WP_CLI::get_runner()->get_project_config_path() );
		if ( is_dir( "{$this->source}/plugins" ) ) {
			$this->link_plugins( "{$this->source}/plugins" );
		}

		$is_theme = false;

		$slug = $this->theme_slug();
		if ( $slug ) {
			$type     = 'theme';
			$target   = WP_CONTENT_DIR . '/themes';
			$is_theme = true;
		} else {
			$type   = 'plugin';
			$slug   = $this->plugin_slug();
			$target = WP_PLUGIN_DIR;
		}
		$this->link_path( $this->source, "$target/$slug" );
		$message =
		WP_CLI::success(
			WP_CLI\Utils\esc_cmd(
				"Linked $type from %s to %s.",
				$this->source,
				"$target/$slug"
			)
		);
		if ( $is_theme ) {
			if ( is_array( WP_CLI::get_runner()->find_command_to_run( array( 'ln-i18n' ) ) ) ) {
				WP_CLI::runcommand( 'ln-i18n' );
			}
		}
	}

	private function rm( $path ) {
		if ( file_exists( $path ) ) {
			$cmd = 'rm -' . ( is_dir( $path ) ? 'r' : '' ) . 'f %s';
			passthru( WP_CLI\Utils\esc_cmd( $cmd, $path ) );
		}
	}

	private function link_path( $source, $target ) {
		$this->rm( $target );
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

	private function theme_slug() {
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
				$slug = trim( trim( $matches[1] ), '/' );
			}
		}
		return $slug;
	}

	private function plugin_slug() {
		$slug = false;
		foreach ( glob( $this->source . '/*.php' ) as $php_file ) {
			$contents = file_get_contents( $php_file, false, null, 0, 5000 );
			if ( preg_match( '#\s*\*?\s*Plugin Name:#', $contents, $matches ) ) {
				$slug = preg_replace( '/\.php$/', '', basename( $php_file ) );
			}
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
WP_CLI::add_command( 'link', 'Link_Command' );
WP_CLI::add_command( 'ln', 'Link_Command' );
