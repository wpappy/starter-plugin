<?php

namespace Wpappy_1_0_5\Helper;

use Wpappy_1_0_5\App;
use Wpappy_1_0_5\Core;

defined( 'ABSPATH' ) || exit;

trait Featured {

	protected function get_feature( ?App $app, ?Core $core, string $prop, string $classname ) /* mixed */ {
		if ( ! is_a( $this->$prop, $classname ) ) {
			$this->$prop = $app ?
				( $core ?
					new $classname( $app, $core ) :
					new $classname( $app )
				) : new $classname( $core );
		}

		return $this->$prop;
	}
}
