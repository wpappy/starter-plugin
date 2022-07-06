<?php

namespace My_Plugin;

defined( 'ABSPATH' ) || exit;

final class Setup {

	public function __construct() {
		if ( app()->simpleton()->validate( self::class ) ) {
			return;
		}

		new Config();

		app()->setup( array( $this, 'setup' ) );
	}

	public function setup(): void {
		new Setting();
		new Singular();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		app()->hook()->do_action( 'setup_complete' );
	}

	public function enqueue_assets(): void {
		app()->setting()->context()->add( 'general', 'single' );

		$label = app()->setting()->get( 'hello_text_label', 'labels' );

		app()->asset()->enqueue_script(
			'main',
			array( 'jquery' ),
			array(
				'labelText' => $label,
			)
		)->asset()->enqueue_style( 'main' );
	}
}
