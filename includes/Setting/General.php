<?php

namespace My_Plugin\Setting;

use function My_Plugin\app;

defined( 'ABSPATH' ) || exit;

final class General {

	public function __construct() {
		if ( app()->simpleton()->validate( self::class ) ) {
			return;
		}

		app()->setting()->add_tab(
			'single',
			app()->i18n()->__( 'Single Post' )
		);

		app()->setting()->add_box(
			'labels'
		)->setting()->add(
			'hello_text_label',
			'textarea',
			app()->i18n()->__( '"Hello" text label' ),
			array(
				'default' => app()->i18n()->__( 'Hello from the plugin!' ),
			)
		);
	}
}
