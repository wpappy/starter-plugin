<?php

namespace Wpappy_1_0_4;

defined( 'ABSPATH' ) || exit;

/**
 * Manage navigation menus.
 */
class Nav_Menu extends Feature {

	public function add( string $slug, string $title ): App {
		register_nav_menus( array( $this->app->get_key( $slug ) => $title ) );

		return $this->app;
	}
}
