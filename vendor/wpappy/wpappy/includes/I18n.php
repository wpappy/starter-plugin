<?php

namespace Wpappy_1_0_5;

defined( 'ABSPATH' ) || exit;

/**
 * Manage i18n (translations) for the application.
 */
class I18n extends Feature {

	protected $dirname = 'languages';

	/**
	 * Set a name for the directory that contains the i18n files.
	 *
	 * Default: `'languages'`.
	 */
	public function set_dirname( string $dirname ): App {
		$this->set_property( 'dirname', $dirname );

		return $this->app;
	}

	/**
	 * Get a path to the directory that contains the i18n files.
	 */
	public function get_path( bool $rel = false ): string {
		$this->validate_setup();

		return $rel ? $this->dirname : $this->get_path( $this->dirname );
	}

	/**
	 * Get a URL of the directory that contains the i18n files.
	 */
	public function get_url(): string {
		$this->validate_setup();

		return $this->app->get_url( $this->dirname );
	}

	/**
	 * Required.
	 */
	public function setup(): App {
		$this->add_setup_action(
			__FUNCTION__,
			function (): void {
				if ( $this->is_theme() ) {
					load_theme_textdomain( $this->app->get_key(), $this->get_path() );

				} else {
					load_plugin_textdomain( $this->app->get_key(), false, $this->get_path() );
				}
			}
		);

		return $this->app;
	}

	public function localize_script( string $slug ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		wp_set_script_translations( $this->app->asset()->get_key( $slug ), $this->app->get_key(), $this->get_path() );

		return $this->app;
	}

	public function __( string $text ): string {
		return translate( $text, $this->validate_setup() ? 'default' : $this->app->get_key() ); // phpcs:ignore
	}

	public function _x( string $text, string $context ): string {
		return translate_with_gettext_context( $text, $context, $this->validate_setup() ? 'default' : $this->app->get_key() ); // phpcs:ignore
	}

	public function _n( string $single, string $plural, int $number ): string {
		return _n( $single, $plural, $number, $this->validate_setup() ? 'default' : $this->app->get_key() ); // phpcs:ignore
	}
}
