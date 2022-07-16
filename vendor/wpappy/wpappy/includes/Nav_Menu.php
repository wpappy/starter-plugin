<?php

namespace Wpappy_1_0_5;

defined( 'ABSPATH' ) || exit;

/**
 * Manage navigation menus.
 */
class Nav_Menu extends Feature {

	public function add( string $slug, string $title ): App {
		register_nav_menus( array( $this->app->get_key( $slug ) => $title ) );

		return $this->app;
	}

	public function render( string $slug, array $args, ?string $before = null, ?string $after = null ): App {
		if ( ! has_nav_menu( $this->app->get_key( $slug ) ) ) {
			return $this->app;
		}

		$args = wp_parse_args(
			$args,
			array(
				'theme_location' => $this->app->get_key( $slug ),
				'container'      => 'ul'
			)
		);

		if ( $before ) {
			echo $before;
		}

		wp_nav_menu( $args );

		if ( $after ) {
			echo $after;
		}

		return $this->app;
	}
}
