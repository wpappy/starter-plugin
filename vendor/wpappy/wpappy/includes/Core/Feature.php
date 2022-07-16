<?php

namespace Wpappy_1_0_5\Core;

use Wpappy_1_0_5\Core;
use Wpappy_1_0_5\Helper;

defined( 'ABSPATH' ) || exit;

abstract class Feature {

	use Helper\Featured;
	use Helper\Single_Call;

	protected $core;

	public function __construct( Core $core ) {
		$this->core = $core;
	}

	protected function is_theme(): bool {
		return 'theme' === $this->core->get_env();
	}

	protected function add_setup_action( string $function, callable $callback, int $priority = 1 ): void {
		if ( $this->validate_single_call( $function, true ) ) {
			return;
		}

		add_action( $this->is_theme() ? 'after_setup_theme' : 'plugins_loaded', $callback, $priority );
	}
}
