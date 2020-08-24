<?php
/**
 * F9wishlist setup
 *
 * @package F9wishlist
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main F9wishlist Class.
 *
 * @class F9wishlist
 */
final class F9wishlist {

	/**
	 * F9wishlist version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var F9wishlist
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Main F9wishlist Instance.
	 *
	 * Ensures only one instance of F9wishlist is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WC()
	 * @return F9wishlist - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * F9wishlist Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		register_activation_hook( F9WISHLIST_PLUGIN_FILE, array( 'F9wishlist_Install', 'install' ) );

		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		if ( $this->is_request( 'frontend' ) ) {
			add_action( 'woocommerce_init', array( $this, 'frontend_includes' ) );
		}
	}

	/**
	 * Define F9wishlist Constants.
	 */
	private function define_constants() {
		$this->define( 'F9WISHLIST_ABSPATH', dirname( F9WISHLIST_PLUGIN_FILE ) . '/' );
		$this->define( 'F9WISHLIST_VERSION', $this->version );
		$this->define( 'F9WISHLIST_TEMPLATE_DEBUG_MODE', false );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Returns true if the request is a non-legacy REST API request.
	 *
	 * Legacy REST requests should still run some extra code for backwards compatibility.
	 *
	 * @todo: replace this function once core WP function is available: https://core.trac.wordpress.org/ticket/42061.
	 *
	 * @return bool
	 */
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return apply_filters( 'f9wishlist_is_rest_api_request', $is_rest_api_request );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		/**
		 * Core classes.
		 */
		include_once F9WISHLIST_ABSPATH . 'includes/f9wishlist-core-functions.php';
		include_once F9WISHLIST_ABSPATH . 'includes/class-f9wishlist-install.php';

		if ( $this->is_request( 'admin' ) ) {
			include_once F9WISHLIST_ABSPATH . 'includes/admin/class-f9wishlist-admin.php';
		}

		$this->theme_support_includes();
	}

	/**
	 * Include classes for theme support.
	 *
	 * @since 1.0.0
	 */
	private function theme_support_includes() {
		if ( 'storefront' === get_template() ) {
			include_once F9WISHLIST_ABSPATH . 'includes/theme-support/class-f9wishlist-storefront.php';
		}
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once F9WISHLIST_ABSPATH . 'includes/class-f9wishlist-frontend-scripts.php';
		include_once F9WISHLIST_ABSPATH . 'includes/f9wishlist-template-hooks.php';
	}

	/**
	 * Function used to Init F9wishlist Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once F9WISHLIST_ABSPATH . 'includes/f9wishlist-template-functions.php';
	}

	/**
	 * Init F9wishlist when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_f9wishlist_init' );

		// Set up localisation.
		$this->load_plugin_textdomain();

		// Init action.
		do_action( 'f9wishlist_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/f9wishlist/f9wishlist-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/f9wishlist-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			// @todo Remove when start supporting WP 5.0 or later.
			$locale = is_admin() ? get_user_locale() : get_locale();
		}

		$locale = apply_filters( 'plugin_locale', $locale, 'f9wishlist' );

		unload_textdomain( 'f9wishlist' );
		load_textdomain( 'f9wishlist', WP_LANG_DIR . '/f9wishlist/f9wishlist-' . $locale . '.mo' );
		load_plugin_textdomain( 'f9wishlist', false, plugin_basename( dirname( F9WISHLIST_PLUGIN_FILE ) ) . '/languages' );
		load_textdomain( 'f9wishlist', dirname( F9WISHLIST_PLUGIN_FILE ) . '/languages/' . $locale . '.mo' );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( F9WISHLIST_PLUGIN_FILE ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		$wc_template_path = apply_filters( 'woocommerce_template_path', 'woocommerce' );
		return apply_filters( 'fervidum_wc_template_path', $wc_template_path );
	}
}
