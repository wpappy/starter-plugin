<?php

namespace Wpappy_1_0_6\Integration;

use Wpappy_1_0_6\App;

defined( 'ABSPATH' ) || exit;

/**
 * Polylang.
 */
class Polylang extends Plugin {

	/**
	 * Is Polylang active.
	 */
	public function is_active(): bool {
		return class_exists( 'Polylang', false );
	}

	/**
	 * Required.
	 */
	public function setup(): App {
		$this->add_setup_action(
			__FUNCTION__,
			function (): void {
				if ( ! $this->is_active() ) {
					return;
				}

				$this->change_home_url();
			}
		);

		return $this->app;
	}

	protected function change_home_url(): void {
		$this->core->hook()->add_filter(
			'home_url',
			function ( string $path, string $rel_path, $base ): string {
				return $base ? $path : ( pll_home_url() . ( '/' === $rel_path ? '' : $rel_path ) );
			},
			10,
			3
		);
	}
}
