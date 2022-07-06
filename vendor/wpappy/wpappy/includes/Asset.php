<?php

namespace Wpappy_1_0_4;

defined( 'ABSPATH' ) || exit;

/**
 * Manage static assets.
 */
class Asset extends Feature {

	protected $dirname        = 'assets';
	protected $script_dirname = 'scripts';
	protected $style_dirname  = 'styles';
	protected $postfix        = '.min';

	/**
	 * Default: 'assets'.
	 */
	public function set_dirname( string $name ): App {
		$this->set_property( 'dirname', $name );

		return $this->app;
	}

	/**
	 * Default: 'scripts'.
	 */
	public function set_script_dirname( string $name ): App {
		$this->set_property( 'script_dirname', $name );

		return $this->app;
	}

	/**
	 * Default: 'styles'.
	 */
	public function set_style_dirname( string $name ): App {
		$this->set_property( 'style_dirname', $name );

		return $this->app;
	}

	/**
	 * Default: '.min'.
	 */
	public function set_postfix( string $postfix ): App {
		$this->set_property( 'postfix', $postfix );

		return $this->app;
	}

	protected function get_rel( string $path = '' ): string {
		return $this->dirname . ( $path ? DIRECTORY_SEPARATOR . $path : '' );
	}

	public function get_path( string $path = '', bool $rel = false ): string {
		return $rel ? $this->get_rel( $path ) : $this->app->get_path( $this->get_rel( $path ) );
	}

	public function get_url( string $url = '', bool $stamp = false ): string {
		return $this->app->get_url( $this->get_rel( $url ), $stamp );
	}

	public function get_script_path( string $path = '', bool $rel = false ): string {
		return $this->get_path( $this->script_dirname . ( $path ? DIRECTORY_SEPARATOR . $path : '' ), $rel );
	}

	public function get_script_url( string $url = '', bool $stamp = false ): string {
		return $this->get_url( $this->script_dirname . ( $url ? DIRECTORY_SEPARATOR . $url : '' ), $stamp );
	}

	public function get_style_path( string $path = '', bool $rel = false ): string {
		return $this->get_path( $this->style_dirname . ( $path ? DIRECTORY_SEPARATOR . $path : '' ), $rel );
	}

	public function get_style_url( string $url = '', bool $stamp = false ): string {
		return $this->get_url( $this->style_dirname . ( $url ? DIRECTORY_SEPARATOR . $url : '' ), $stamp );
	}

	public function get_key( string $slug ): string {
		return $this->app->get_key( str_replace( '-', '_', $slug ) );
	}

	public function enqueue_script( string $slug, array $deps = array(), array $args = array(), ?string $args_object_name = null, bool $in_footer = true ): App {
		$key      = $this->get_key( $slug );
		$filename = $slug . $this->postfix . '.js';
		$url      = $this->get_script_url( $filename );
		$path     = $this->get_script_path( $filename );

		if ( ! file_exists( $path ) ) {
			return $this->app;
		}

		wp_enqueue_script( $key, $url, $deps, filemtime( $path ), $in_footer );

		if ( $args ) {
			$args_object_name = $args_object_name ?: $this->core->type()->str()->to_camelcase( $key );

			wp_localize_script( $key, $args_object_name, $args );
		}

		return $this->app;
	}

	/**
	 * Apply an inline script to the script file.
	 *
	 * @param string $position `'after'` or `'before'`.
	 */
	public function enqueue_inline_script( string $parent_slug, string $js_code, string $position = 'after' ): App {
		wp_add_inline_script( $this->app->get_key( $parent_slug ), $js_code, $position );

		return $this->app;
	}

	public function enqueue_style( string $slug, array $deps = array(), /* string|array */ $addition = null ): App {
		$key      = $this->get_key( $slug );
		$filename = $slug . $this->postfix . '.css';
		$url      = $this->get_style_url( $filename );
		$path     = $this->get_style_path( $filename );

		if ( ! file_exists( $path ) ) {
			return $this->app;
		}

		wp_enqueue_style( $key, $url, $deps, filemtime( $path ) );

		if ( empty( $addition ) ) {
			return $this->app;
		}

		if ( is_string( $addition ) ) {
			wp_add_inline_style( $key, $addition );

		} elseif ( is_array( $addition ) ) {
			$css_vars = ':root{';

			foreach ( $addition as $var_slug => $var_val ) {
				$css_vars .= '--' . str_replace( '_', '-', $this->app->get_key( $var_slug ) ) . ':' . $var_val . ';';
			}

			wp_add_inline_style( $key, "$css_vars}" );
		}

		return $this->app;
	}

	public function get_global_args_key( string $js_object_name ): string {
		return $this->app->get_key( "args_object_$js_object_name" );
	}

	public function enqueue_global_args( string $js_object_name, array $args ): App {
		$key = $this->get_global_args_key( $js_object_name );

		wp_register_script( $key, null, array(), null ); // phpcs:ignore
		wp_localize_script( $key, $js_object_name, $args );

		return $this->app;
	}

	public function enqueue_external_script( string $slug, string $url, bool $in_footer = true ): App {
		wp_enqueue_script( $this->app->get_key( $slug ), $url, false, null, $in_footer ); // phpcs:ignore

		return $this->app;
	}

	public function enqueue_external_style( string $slug, string $url ): App {
		wp_enqueue_style( $this->app->get_key( $slug ), $url, false, null ); // phpcs:ignore

		return $this->app;
	}
}
