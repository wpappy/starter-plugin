<?php

namespace Wpappy_1_0_5\Media;

use Wpappy_1_0_5\Feature;

defined( 'ABSPATH' ) || exit;

/**
 * Manage media uploads.
 */
class Upload extends Feature {

	public function get_path( $path = '' ): string {
		return $this->core->media()->upload()->get_path( $path );
	}

	public function get_url( $url = '' ): string {
		return $this->core->media()->upload()->get_url( $url );
	}
}
