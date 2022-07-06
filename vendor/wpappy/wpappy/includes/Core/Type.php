<?php

namespace Wpappy_1_0_4\Core;

defined( 'ABSPATH' ) || exit;

class Type extends Feature {

	protected $str;

	public function str(): Type\Str {
		return $this->get_feature( null, $this->core, 'str', Type\Str::class );
	}
}
