<?php

namespace Wpappy_1_0_6;

defined( 'ABSPATH' ) || exit;

/**
 * Manage media.
 */
class Media extends Feature {

	protected $upload;

	public function add_image_size( string $slug, int $width, int $height = 0, bool $crop = false ): App {
		add_image_size( $this->app->get_key( $slug ), $width, $height, $crop );

		return $this->app;
	}

	/**
	 * Manage media uploads.
	 */
	public function upload(): Media\Upload {
		return $this->get_feature( $this->app, $this->core, 'upload', Media\Upload::class );
	}
}
