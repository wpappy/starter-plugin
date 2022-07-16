<?php

namespace Wpappy_1_0_5\Core;

defined( 'ABSPATH' ) || exit;

class Http extends Feature {

	public function get_current_url( ?bool $is_admin = null ): string {
		return $this->get_home_url(
			add_query_arg( null, null ),
			$is_admin ?? is_admin()
		);
	}

	public function get_home_url( string $path = '/', bool $without_filters = false ): string {
		$url = home_url( $path );

		return $without_filters ?
			$url :
			$this->core->hook()->apply_filters( 'home_url', $url, $path );
	}

	public function get_root_host(): string {
		$host  = wp_parse_url( $this->get_home_url() )['host'];
		$parts = explode( '.', $host );

		return end( $parts );
	}

	public function redirect( string $url, ?callable $callback = null ): void {
		header( "Location: $url" );

		if ( $callback ) {
			header_register_callback( $callback );
		}
	}
}
