<?php

namespace Wpappy_1_0_5\Core\Media;

use Wpappy_1_0_5\Core\Feature;

defined( 'ABSPATH' ) || exit;

class Upload extends Feature {

	public function get_path( $path = '' ): string {
		return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'uploads' . ( $path ? ( DIRECTORY_SEPARATOR . $path ) : '' );
	}

	public function get_url( $url = '' ): string {
		return WP_CONTENT_URL . DIRECTORY_SEPARATOR . 'uploads' . ( $url ? ( DIRECTORY_SEPARATOR . $url ) : '' );
	}
}
