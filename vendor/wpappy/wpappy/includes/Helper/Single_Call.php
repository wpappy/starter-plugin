<?php

namespace Wpappy_1_0_5\Helper;

defined( 'ABSPATH' ) || exit;

trait Single_Call {

	protected $single_calls = array();

	protected function validate_single_call( string $function, bool $enable_silent = false ): bool {
		$is_called = $this->is_single_called( $function );

		if ( ! $enable_silent && $is_called ) {
			$this->core->debug()->die( "<code>$function</code> can be called only once." );
		}

		$this->single_calls[] = $function;

		return $is_called;
	}

	protected function is_single_called( string $function ): bool {
		return in_array( $function, $this->single_calls, true );
	}
}
