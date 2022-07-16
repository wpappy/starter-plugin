<?php

namespace Wpappy_1_0_5\Core;

defined( 'ABSPATH' ) || exit;

class Asset extends Feature {

	protected $dirname        = 'assets';
	protected $script_dirname = 'scripts';
	protected $style_dirname  = 'styles';
	protected $postfix        = '.min';

	public function get_key( string $slug ): string {
		return $this->core->get_key( str_replace( '-', '_', $slug ) );
	}

	public function enqueue_script( string $slug, array $deps = array(), array $args = array(), ?string $args_object_name = null, bool $in_footer = true ): void {
		$key      = $this->get_key( $slug );
		$filename = $slug . $this->postfix . '.js';
		$file     = $this->dirname . DIRECTORY_SEPARATOR . $this->script_dirname . DIRECTORY_SEPARATOR . $filename;
		$url      = $this->core->fs()->get_url( $file );
		$path     = $this->core->fs()->get_path( $file );

		wp_enqueue_script( $key, $url, $deps, filemtime( $path ), $in_footer );

		if ( $args ) {
			$args_object_name = $args_object_name ?: $this->core->type()->str()->to_camelcase( $key );

			wp_localize_script( $key, $args_object_name, $args );
		}
	}

	public function enqueue_style( string $slug, array $deps = array() ): void {
		$key      = $this->get_key( $slug );
		$filename = $slug . $this->postfix . '.css';
		$file     = $this->dirname . DIRECTORY_SEPARATOR . $this->style_dirname . DIRECTORY_SEPARATOR . $filename;
		$url      = $this->core->fs()->get_url( $file );
		$path     = $this->core->fs()->get_path( $file );

		wp_enqueue_style( $key, $url, $deps, filemtime( $path ) );
	}
}
