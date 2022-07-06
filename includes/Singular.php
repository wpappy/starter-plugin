<?php

namespace My_Plugin;

defined( 'ABSPATH' ) || exit;

final class Singular {

	public function __construct() {
		if ( app()->simpleton()->validate( self::class ) ) {
			return;
		}

		add_filter( 'the_content', array( $this, 'render_hello_text_label' ) );
	}

	public function render_hello_text_label( string $content ): string {
		if ( ! is_single() ) {
			return $content;
		}

		app()->setting()->context()->add( 'general', 'single' );

		$label = app()->setting()->get( 'hello_text_label', 'labels' );

		if ( empty( $label ) ) {
			return $content;
		}

		$content .= app()->template()->get(
			'label',
			array(
				'label' => $label,
			)
		);

		return $content;
	}
}
