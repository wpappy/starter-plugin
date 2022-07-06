<?php

namespace Wpappy_1_0_4\Customizer\Control;

defined( 'ABSPATH' ) || exit;

/**
 * Type: 'line'.
 */
class Line extends \WP_Customize_Control {

	/** @ignore */
	public $type = 'wpappy_line';

	protected function render_content() {
		?>
		<div class="wpappy-control-line"></div>
		<?php
	}
}
