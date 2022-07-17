<?php

namespace Wpappy_1_0_6;

defined( 'ABSPATH' ) || exit;

/**
 * The Entry Point. The best point to start exploring the library.
 *
 * ### Getting Started
 * A method that returns instance of a class that extends the `Feature` class is a specific feature, such as helpers, managers, etc.\
 * Of course, a feature contains its own methods and may also contain its own sub-features.\
 * <a href="https://github.com/wpappy/wpappy#examples" target="_blank">Explore an examples</a> of the Wpappy usage for more details.
 *
 * ### Setup Methods
 * Some features have `::setup()` method. Calling of this method is __required__ when you want to start use this feature.\
 * Don't hide its call in any hooks, as this may ruin the work of the feature.
 *
 * ### Setter Methods
 * In addition, some features also have setter methods, like `::set_<name>(<property>)`. These methods can be optionally used for configure the feature and can change its behavior.\
 * It must be called before any `::setup()` methods are called.
 */
class App {

	use Helper\Featured;
	use Helper\Single_Call;

	protected static $instances = array();

	protected $key;
	protected $root_file;
	protected $env;
	protected $core;
	protected $priority;
	protected $requires_wp  = '5.0.0';
	protected $requires_php = '7.2.0';
	protected $admin;
	protected $ajax;
	protected $asset;
	protected $customizer;
	protected $debug;
	protected $hook;
	protected $http;
	protected $i18n;
	protected $info;
	protected $integration;
	protected $media;
	protected $nav_menu;
	protected $setting;
	protected $simpleton;
	protected $template;
	protected $type;

	/** @ignore */
	protected function __construct( string $key, string $root_file ) {
		$this->set_key( $key );

		$this->root_file = $root_file;
		$root_file_paths = explode( DIRECTORY_SEPARATOR, $root_file );
		$root_dir_number = count( $root_file_paths ) - 3;
		$isset_root_dir  = isset( $root_file_paths[ $root_dir_number ] );
		$is_theme        = $isset_root_dir && 'themes' === $root_file_paths[ $root_dir_number ];
		$is_plugin       = $isset_root_dir && 'plugins' === $root_file_paths[ $root_dir_number ];
		$this->env       = $is_theme ? 'theme' : 'plugin';

		if ( ! $is_theme && ! $is_plugin ) {
			Core\Debug::raw_die( 'Wrong the application root file.', null, $this->key );
		}

		$this->core()->debug()->setup();

		$app_textdomain   = $this->core()->info()->get_textdomain();
		$app_requires_wp  = $this->core()->info()->get_requires_wp();
		$app_requires_php = $this->core()->info()->get_requires_php();

		if ( $app_textdomain && $app_textdomain !== $this->key ) {
			$this->core()->debug()->die( "The textdomain in the application metadata must match the application key <code>'$key'</code>." );
		}

		if ( $app_requires_wp && version_compare( $this->requires_wp, $app_requires_wp, '>' ) ) {
			$this->core()->debug()->die( "Since application uses Wpappy, it must have at least WordPress $this->requires_wp requirement in metadata." );
		}

		if ( $app_requires_php && version_compare( $this->requires_php, $app_requires_php, '>' ) ) {
			$this->core()->debug()->die( "Since application uses Wpappy, it must have at least PHP $this->requires_php requirement in metadata." );
		}
	}

	protected function __clone() {}

	/** @ignore */
	public function __wakeup() {
		$this->core()->debug()->die( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Get the singleton instance of Wpappy for your application.
	 *
	 * @param string $key The application key. It's best to use the `__NAMESPACE__` constant on initial call. Only "_" is allowed as a word separator. Also key will be auto-converted to lowercase. WARNING: be careful when changing this parameter in a live application because it'll make the application as new to the environment.
	 * @param string $root_file Use the `__FILE__` constant of the application's root file (index.php / functions.php). Required only on initial call.
	 */
	public static function get( string $key, string $root_file = '' ): self {
		if ( empty( self::$instances[ $key ] ) ) {
			if ( empty( $root_file ) ) {
				Core\Debug::raw_die( 'The application root file is required on the initial call.', null, $key );
			}

			self::$instances[ $key ] = new self( $key, $root_file );
		}

		return self::$instances[ $key ];
	}

	protected function set_key( string $key ): void {
		if (
			strpos( $key, '-' ) ||
			strpos( $key, '/' ) ||
			strpos( $key, '\\' )
		) {
			Core\Debug::raw_die( 'Only "_" is allowed as a word separator of the application key.', null, $key );
		}

		$this->key = strtolower( $key );
	}

	/**
	 * Get the application key.
	 */
	public function get_key( string $slug = '', string $case = '_' ): string {
		$key = $this->key . ( $slug ? ( "_$slug" ) : '' );

		switch ( $case ) {
			case '-':
				return str_replace( '_', '-', $key );

			case 'c':
				return $this->core()->type()->str()->to_camelcase( $key );

			default:
			case '_':
				return $key;
		}
	}

	/**
	 * Get the application root file.
	 */
	public function get_root_file(): string {
		return $this->root_file;
	}

	/**
	 * Get the application environment.
	 *
	 * @return string `'plugin'` or `'theme'`
	 */
	public function get_env(): string {
		return $this->env;
	}

	public function get_path( string $path = '' ): string {
		return $this->core()->fs()->get_app_path( $path );
	}

	public function get_url( string $url = '', bool $stamp = false ): string {
		return $this->core()->fs()->get_app_url( $url, $stamp );
	}

	protected function is_theme(): bool {
		return 'theme' === $this->get_env();
	}

	/**
	 * Required. Call the application setup action.
	 *
	 * Must be called after all other `::setup()` methods.\
	 * This is a special wrapper for `'plugins_loaded'` or `'after_setup_theme'` hook, depending on the environment. It also contains logic to ensure the stability of the application.\
	 * In the theme environment will be auto-initialized support for the basic features of the theme:
	 * ``` php
	 * add_theme_support( 'title-tag' );
	 * add_theme_support( 'automatic-feed-links' );
	 * add_theme_support( 'post-thumbnails' );
	 * add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );
	 * add_theme_support( 'customize-selective-refresh-widgets' );
	 * ```
	 */
	public function setup( callable $callback, int $priority = 10 ): self {
		if ( $this->validate_single_call( __FUNCTION__ ) ) {
			return $this;
		}

		$this->priority = $priority;

		if ( $this->is_theme() ) {
			add_action(
				'after_setup_theme',
				function (): void {
					add_theme_support( 'title-tag' );
					add_theme_support( 'automatic-feed-links' );
					add_theme_support( 'post-thumbnails' );
					add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );
					add_theme_support( 'customize-selective-refresh-widgets' );
				},
				1
			);
		}

		add_action( $this->is_theme() ? 'after_setup_theme' : 'plugins_loaded', $callback, $priority );

		return $this;
	}

	/**
	 * Get the priority of the setup action that was passed in the call.
	 */
	public function get_priority(): int {
		return $this->priority;
	}

	/**
	 * Is the setup action called.
	 */
	public function is_setup_called(): bool {
		return $this->is_single_called( 'setup' );
	}

	protected function core(): Core {
		return $this->get_feature( $this, null, 'core', Core::class );
	}

	/**
	 * Features used only for the admin panel.
	 */
	public function admin(): Admin {
		return $this->get_feature( $this, $this->core(), 'admin', Admin::class );
	}

	/**
	 * Manage ajax actions.
	 */
	public function ajax(): Ajax {
		return $this->get_feature( $this, $this->core(), 'ajax', Ajax::class );
	}

	/**
	 * Manage static assets.
	 */
	public function asset(): Asset {
		return $this->get_feature( $this, $this->core(), 'asset', Asset::class );
	}

	/**
	 * Manage customizer settings.
	 */
	public function customizer(): Customizer {
		return $this->get_feature( $this, $this->core(), 'customizer', Customizer::class );
	}

	/**
	 * Manage debugging.
	 */
	public function debug(): Debug {
		return $this->get_feature( $this, $this->core(), 'debug', Debug::class );
	}

	/**
	 * Manage the inner hooks for the application.
	 */
	public function hook(): Hook {
		return $this->get_feature( $this, $this->core(), 'hook', Hook::class );
	}

	/**
	 * Helpers for working with URLs and redirects.
	 */
	public function http(): Http {
		return $this->get_feature( $this, $this->core(), 'http', Http::class );
	}

	/**
	 * Manage i18n (translations) for the application.
	 */
	public function i18n(): I18n {
		return $this->get_feature( $this, $this->core(), 'i18n', I18n::class );
	}

	/**
	 *  Information from the application metadata.
	 */
	public function info(): Info {
		return $this->get_feature( $this, $this->core(), 'info', Info::class );
	}

	/**
	 * Helpers for working with popular third-party WordPress plugins.
	 */
	public function integration(): Integration {
		return $this->get_feature( $this, $this->core(), 'integration', Integration::class );
	}

	/**
	 * Helpers for working with data types.
	 */
	public function type(): Type {
		return $this->get_feature( $this, $this->core(), 'type', Type::class );
	}

	/**
	 * Manage navigation menus.
	 */
	public function nav_menu(): Nav_Menu {
		return $this->get_feature( $this, $this->core(), 'nav_menu', Nav_Menu::class );
	}

	/**
	 * Manage settings.
	 */
	public function setting(): Setting {
		return $this->get_feature( $this, $this->core(), 'setting', Setting::class );
	}

	/**
	 * Manage application classes that used the simpleton pattern.
	 */
	public function simpleton(): Simpleton {
		return $this->get_feature( $this, $this->core(), 'simpleton', Simpleton::class );
	}

	/**
	 * Manage template parts.
	 */
	public function template(): Template {
		return $this->get_feature( $this, $this->core(), 'template', Template::class );
	}

	/**
	 * Manage media.
	 */
	public function media(): Media {
		return $this->get_feature( $this, $this->core(), 'media', Media::class );
	}
}
