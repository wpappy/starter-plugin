<?php

namespace Wpappy_1_0_5\Core;

defined( 'ABSPATH' ) || exit;

class Admin extends Feature {

	protected $notice;

	public function notice(): Admin\Notice {
		return $this->get_feature( null, $this->core, 'notice', Admin\Notice::class );
	}
}
