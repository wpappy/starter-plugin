<?php

namespace Wpappy_1_0_4;

defined( 'ABSPATH' ) || exit;

/**
 * Manage template parts.
 */
class Template extends Feature {

	protected $path;

	/** @ignore */
	public function __construct( App $app, Core $core ) {
		parent::__construct( $app, $core );

		$this->path = $this->is_theme() ? 'template-parts' : 'templates';
	}

	public function set_base_path( string $path ): App {
		$this->set_property( 'path', $path );

		return $this->app;
	}

	public function get_path( string $path = '' ): string {
		return $this->app->get_path( $this->path . ( $path ? ( DIRECTORY_SEPARATOR . $path ) : '' ) );
	}

	public function get( string $name, array $args = array() ): string {
		$args = wp_parse_args( $args );

		ob_start();

		if ( $this->is_theme() ) {
			$this->render( $name, $args );

		} else {
			include $this->get_path( "$name.php" );
		}

		return ob_get_clean();
	}

	public function render( string $name, array $args = array() ): App {
		if ( $this->is_theme() ) {
			get_template_part( $this->path . DIRECTORY_SEPARATOR . $name, null, $args );

		} else {
			echo $this->get( $name, $args ); // phpcs:ignore
		}

		return $this->app;
	}
}
