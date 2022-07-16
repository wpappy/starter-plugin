<?php

namespace Wpappy_1_0_5;

defined( 'ABSPATH' ) || exit;

/**
 * Helpers for working with data types.
 */
class Type extends Feature {

	protected $arr;
	protected $str;

	/**
	 * Helpers for working with arrays.
	 */
	public function arr(): Type\Arr {
		return $this->get_feature( $this->app, $this->core, 'arr', Type\Arr::class );
	}

	/**
	 * Helpers for working with text strings.
	 */
	public function str(): Type\Str {
		return $this->get_feature( $this->app, $this->core, 'str', Type\Str::class );
	}
}
