<?php

namespace My_Plugin;

defined( 'ABSPATH' ) || exit;

final class Setting {

	public function __construct() {
		if ( app()->simpleton()->validate( self::class ) ) {
			return;
		}

		app()->setting()->add_page(
			'general',
			'edit.php',
			app()->i18n()->__( 'My Plugin' ),
			app()->i18n()->__( 'General Settings' )
		);

		new Setting\General();
	}

	public static function render_header(): void {
		app()->asset()->enqueue_script( 'setting', array( 'jquery' ) )
			->asset()->enqueue_style( 'setting' )
			->template()->render( 'setting/header' );
	}
}
