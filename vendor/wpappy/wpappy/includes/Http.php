<?php

namespace Wpappy_1_0_4;

defined( 'ABSPATH' ) || exit;

/**
 * Helpers for working with URLs and redirects.
 */
class Http extends Feature {

	public function get_current_url( bool $rel = false ): string {
		return $this->core->http()->get_current_url( $rel );
	}

	public function get_home_url( string $path = '/', bool $base = false ): string {
		return $this->core->http()->get_home_url( $path, $base );
	}

	public function get_root_host(): string {
		return $this->core->http()->get_root_host();
	}

	public function redirect( string $url, ?callable $callback = null ): App {
		$this->core->http()->redirect( $url, $callback );

		return $this->app;
	}

	public function get_tel_url( string $tel ): string {
		return 'tel:' . str_replace( array( '-', ' ', '(', ')' ), '', $tel );
	}
}
