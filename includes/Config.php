<?php

namespace My_Plugin;

defined( 'ABSPATH' ) || exit;

final class Config {

	public function __construct() {
		if ( app()->simpleton()->validate( self::class ) ) {
			return;
		}

		app()->i18n()->setup();
		app()->admin()->notice()->setup();

		app()->setting()->set_header( array( Setting::class, 'render_header' ) )
			->setting()->set_submit_btn( app()->i18n()->__( 'Save changes' ) )
			->setting()->set_error_notice( app()->i18n()->__( 'Something went wrong.' ) )
			->setting()->set_success_notice( app()->i18n()->__( 'Changes saved.' ) )
			->setting()->setup();
	}
}
